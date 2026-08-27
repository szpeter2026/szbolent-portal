use axum::{extract::Query, Extension, Json};
use serde::Deserialize;

use crate::auth::jwt::Claims;
use crate::db::models::{
    PermissionCheckRequest, PermissionCheckResponse, PermissionPolicyResponse,
};
use crate::error::AppError;
use crate::services::permission_service::PermissionService;

use super::AppState;

/// POST /v1/permissions/check — 检查用户权限
pub async fn check_permission(
    Extension(state): Extension<AppState>,
    Extension(claims): Extension<Claims>,
    Json(body): Json<PermissionCheckRequest>,
) -> Result<Json<PermissionCheckResponse>, AppError> {
    let allowed = PermissionService::check(
        &state.casbin_enforcer,
        &claims.sub,
        &body.resource,
        &body.action,
    )
    .await?;

    Ok(Json(PermissionCheckResponse {
        allowed,
        reason: if allowed {
            None
        } else {
            Some(format!(
                "用户 {} 没有 {} 资源的 {} 权限",
                claims.sub, body.action, body.resource
            ))
        },
    }))
}

/// GET /v1/permissions/my 查询参数
#[derive(Deserialize)]
pub struct MyPermissionsQuery {
    #[allow(dead_code)]
    pub product: Option<String>,
}

/// GET /v1/permissions/my — 获取当前用户的权限列表
pub async fn get_my_permissions(
    Query(_params): Query<MyPermissionsQuery>,
    Extension(state): Extension<AppState>,
    Extension(claims): Extension<Claims>,
) -> Result<Json<Vec<PermissionPolicyResponse>>, AppError> {
    let policies = PermissionService::get_user_policies(
        &state.pool,
        &state.casbin_enforcer,
        &claims.sub,
    )
    .await?;

    Ok(Json(policies))
}
