use axum::{http::StatusCode, response::IntoResponse, Json};
use serde_json::json;

/// 应用错误类型
#[derive(Debug, thiserror::Error)]
#[allow(dead_code)]
pub enum AppError {
    #[error("未授权: {0}")]
    Unauthorized(String),

    #[error("权限不足: {0}")]
    Forbidden(String),

    #[error("资源不存在: {0}")]
    NotFound(String),

    #[error("请求参数错误: {0}")]
    BadRequest(String),

    #[error("数据库错误: {0}")]
    Database(#[from] sqlx::Error),

    #[error("Casbin 错误: {0}")]
    Casbin(#[from] casbin::Error),

    #[error("JWT 错误: {0}")]
    Jwt(String),

    #[error("内部错误: {0}")]
    Internal(String),
}

impl IntoResponse for AppError {
    fn into_response(self) -> axum::response::Response {
        let (status, error_code) = match &self {
            AppError::Unauthorized(_) => (StatusCode::UNAUTHORIZED, "unauthorized"),
            AppError::Forbidden(_) => (StatusCode::FORBIDDEN, "forbidden"),
            AppError::NotFound(_) => (StatusCode::NOT_FOUND, "not_found"),
            AppError::BadRequest(_) => (StatusCode::BAD_REQUEST, "bad_request"),
            AppError::Database(_) => (StatusCode::INTERNAL_SERVER_ERROR, "database_error"),
            AppError::Casbin(_) => (StatusCode::INTERNAL_SERVER_ERROR, "casbin_error"),
            AppError::Jwt(_) => (StatusCode::UNAUTHORIZED, "jwt_error"),
            AppError::Internal(_) => (StatusCode::INTERNAL_SERVER_ERROR, "internal_error"),
        };

        let body = json!({
            "error": error_code,
            "message": self.to_string()
        });

        (status, Json(body)).into_response()
    }
}

/// 方便的 Result 类型别名
pub type AppResult<T> = Result<T, AppError>;
