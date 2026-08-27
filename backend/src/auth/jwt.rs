use axum::{
    extract::Request,
    http::header,
    middleware::Next,
    response::Response,
};
use jsonwebtoken::{decode, DecodingKey, Validation};
use serde::{Deserialize, Serialize};

/// JWT Claims
#[derive(Debug, Serialize, Deserialize, Clone)]
pub struct Claims {
    /// 用户 ID
    pub sub: String,
    /// 产品标识
    pub product: Option<String>,
    /// 角色列表
    pub roles: Vec<String>,
    /// VC 凭证类型列表 (YeDall)
    pub vcs: Vec<String>,
    /// 过期时间 (unix timestamp)
    pub exp: usize,
    /// 签发时间
    pub iat: usize,
}

/// 从请求头提取 JWT 字符串
pub fn extract_token(req: &Request) -> Option<String> {
    req.headers()
        .get(header::AUTHORIZATION)
        .and_then(|v| v.to_str().ok())
        .and_then(|v| v.strip_prefix("Bearer "))
        .map(|s| s.to_string())
}

/// 解析 JWT 为 Claims
pub fn verify_token(token: &str, secret: &str) -> Result<Claims, jsonwebtoken::errors::Error> {
    let token_data = decode::<Claims>(
        token,
        &DecodingKey::from_secret(secret.as_bytes()),
        &Validation::default(),
    )?;
    Ok(token_data.claims)
}

/// 从请求扩展中获取当前用户 Claims（在中间件之后可用）
#[allow(dead_code)]
pub fn current_user(req: &Request) -> Option<&Claims> {
    req.extensions().get::<Claims>()
}

// ============================================================
// Axum 中间件 — 通用认证层
// ============================================================

/// 认证中间件（可选模式） — 解析 JWT 但不强制要求
/// 始终注入 Option<Claims> 到 extensions
pub async fn auth_layer(mut req: Request, next: Next) -> Response {
    let jwt_secret = req
        .extensions()
        .get::<JwtSecret>()
        .map(|s| s.0.clone())
        .unwrap_or_default();

    let claims = extract_token(&req)
        .and_then(|t| verify_token(&t, &jwt_secret).ok());

    if let Some(c) = claims {
        req.extensions_mut().insert(c);
    }

    next.run(req).await
}

/// 强制认证中间件 — 未认证返回 401
pub async fn require_auth_layer(mut req: Request, next: Next) -> Result<Response, Response> {
    let jwt_secret = req
        .extensions()
        .get::<JwtSecret>()
        .map(|s| s.0.clone())
        .unwrap_or_default();

    let token = extract_token(&req);

    match token {
        Some(t) => match verify_token(&t, &jwt_secret) {
            Ok(claims) => {
                req.extensions_mut().insert(claims);
                Ok(next.run(req).await)
            }
            Err(_) => {
                let body = serde_json::json!({
                    "error": "unauthorized",
                    "message": "token 无效或已过期"
                });
                let resp = Response::builder()
                    .status(401)
                    .header("Content-Type", "application/json")
                    .body(axum::body::Body::from(body.to_string()))
                    .unwrap();
                Err(resp)
            }
        },
        None => {
            let body = serde_json::json!({
                "error": "unauthorized",
                "message": "缺少认证 token"
            });
            let resp = Response::builder()
                .status(401)
                .header("Content-Type", "application/json")
                .body(axum::body::Body::from(body.to_string()))
                .unwrap();
            Err(resp)
        }
    }
}

/// JWT Secret 包装类型 — 用于在 extensions 间传递
#[derive(Clone)]
pub struct JwtSecret(pub String);

// ============================================================
// 开发辅助
// ============================================================

/// 生成 JWT（开发/测试用）
#[allow(dead_code)]
pub fn generate_token(
    sub: &str,
    roles: Vec<String>,
    vcs: Vec<String>,
    secret: &str,
    expiry_hours: i64,
) -> Result<String, jsonwebtoken::errors::Error> {
    use jsonwebtoken::{encode, EncodingKey, Header};
    use std::time::{SystemTime, UNIX_EPOCH};

    let now = SystemTime::now()
        .duration_since(UNIX_EPOCH)
        .unwrap()
        .as_secs() as usize;

    let claims = Claims {
        sub: sub.to_string(),
        product: None,
        roles,
        vcs,
        exp: now + (expiry_hours as usize * 3600),
        iat: now,
    };

    encode(
        &Header::default(),
        &claims,
        &EncodingKey::from_secret(secret.as_bytes()),
    )
}
