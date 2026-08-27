use axum::{
    extract::{Path, Query},
    http::StatusCode,
    Extension, Json,
};
use serde::{Deserialize, Serialize};

use crate::auth::jwt::Claims;
use crate::db::models::{PageSchemaResponse, PaginatedResponse};
use crate::error::AppError;
use crate::services::page_service::PageService;

use super::AppState;

/// GET /v1/pages/by-path 查询参数
#[derive(Deserialize)]
pub struct GetByPathQuery {
    pub path: String,
}

/// GET /v1/pages/by-path — 获取页面的 Schema（可选认证）
///
/// 返回已按当前用户权限过滤的组件列表。
pub async fn get_page_by_path(
    Query(params): Query<GetByPathQuery>,
    Extension(state): Extension<AppState>,
    claims: Option<Extension<Claims>>,
) -> Result<Json<PageSchemaResponse>, AppError> {
    let pool = &state.pool;

    let page = PageService::get_by_path(pool, &params.path).await?
        .ok_or_else(|| AppError::NotFound(format!("页面不存在: {}", params.path)))?;

    let mut response = PageService::to_response(page);

    // 过滤组件
    let is_authenticated = claims.is_some();
    let user_roles: Vec<String> = claims
        .map(|c| c.roles.clone())
        .unwrap_or_default();

    response.components = PageService::filter_components(
        response.components,
        &user_roles,
        is_authenticated,
    );

    Ok(Json(response))
}

/// GET /v1/pages 查询参数
#[derive(Deserialize)]
pub struct ListPagesQuery {
    pub product: String,
    pub status: Option<String>,
    #[serde(default = "default_page")]
    pub page: i64,
    #[serde(default = "default_limit")]
    pub limit: i64,
}

fn default_page() -> i64 { 1 }
fn default_limit() -> i64 { 20 }

/// GET /v1/pages — 列出产品的所有页面（管理员）
pub async fn list_pages(
    Query(params): Query<ListPagesQuery>,
    Extension(state): Extension<AppState>,
) -> Result<Json<PaginatedResponse<PageSchemaResponse>>, AppError> {
    let pool = &state.pool;

    let (pages, total) = PageService::list_pages(
        pool,
        &params.product,
        params.status.as_deref(),
        params.page,
        params.limit,
    )
    .await?;

    let data: Vec<PageSchemaResponse> = pages
        .into_iter()
        .map(PageService::to_response)
        .collect();

    Ok(Json(PaginatedResponse { data, total }))
}

/// POST /v1/pages — 创建或更新页面 Schema
#[derive(Deserialize, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct UpsertPageBody {
    pub id: Option<String>,
    pub product: String,
    pub path: String,
    #[serde(default = "default_page_type")]
    pub page_type: String,
    #[serde(default = "default_layout")]
    pub layout: String,
    pub title: String,
    pub description: Option<String>,
    #[serde(default = "default_visibility")]
    pub visibility: String,
    pub data_source: Option<String>,
    pub components: Vec<PageComponentBody>,
    pub meta: Option<serde_json::Value>,
    #[serde(default = "default_status")]
    pub status: String,
}

fn default_page_type() -> String { "custom".to_string() }
fn default_layout() -> String { "default".to_string() }
fn default_visibility() -> String { "public".to_string() }
fn default_status() -> String { "draft".to_string() }

#[derive(Deserialize, Serialize)]
#[serde(rename_all = "camelCase")]
pub struct PageComponentBody {
    pub id: String,
    #[serde(rename = "type")]
    pub component_type: String,
    pub props: serde_json::Value,
    pub data_source: Option<String>,
    #[serde(default = "default_visible_to")]
    pub visible_to: String,
    pub required_role: Option<String>,
    pub required_vc: Option<String>,
    #[serde(default)]
    pub order: i32,
}

fn default_visible_to() -> String { "public".to_string() }

pub async fn upsert_page(
    Extension(state): Extension<AppState>,
    Json(body): Json<UpsertPageBody>,
) -> Result<(StatusCode, Json<serde_json::Value>), AppError> {
    let pool = &state.pool;

    let components_json = serde_json::to_value(&body.components)
        .map_err(|e| AppError::BadRequest(format!("组件数据格式错误: {}", e)))?;

    let id = PageService::upsert(
        pool,
        body.id,
        &body.product,
        &body.path,
        &body.page_type,
        &body.layout,
        &body.title,
        body.description.as_deref(),
        &body.visibility,
        body.data_source.as_deref(),
        &components_json,
        body.meta.as_ref(),
        &body.status,
    )
    .await?;

    Ok((
        StatusCode::OK,
        Json(serde_json::json!({"id": id, "message": "页面保存成功"})),
    ))
}

/// DELETE /v1/pages/{id} — 删除页面
pub async fn delete_page(
    Path(id): Path<String>,
    Extension(state): Extension<AppState>,
) -> Result<(StatusCode, Json<serde_json::Value>), AppError> {
    let pool = &state.pool;

    let deleted = PageService::delete(pool, &id).await?;
    if !deleted {
        return Err(AppError::NotFound(format!("页面不存在: {}", id)));
    }

    Ok((
        StatusCode::OK,
        Json(serde_json::json!({"message": "页面已删除"})),
    ))
}
