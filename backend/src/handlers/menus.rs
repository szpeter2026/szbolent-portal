use axum::{
    extract::Query,
    http::StatusCode,
    Extension,
    Json,
};
use serde::Deserialize;

use crate::auth::jwt::Claims;
use crate::db::models::MenuItemResponse;
use crate::error::AppError;
use crate::services::menu_service::MenuService;

use super::AppState;

/// GET /v1/menus 查询参数
#[derive(Deserialize)]
pub struct GetMenusQuery {
    pub product: String,
}

/// GET /v1/menus — 获取用户的可见菜单树（可选认证）
///
/// 未认证用户只能看到 visibleTo=public 的菜单项。
/// 已认证用户能看到对应角色的菜单项。
pub async fn get_menus(
    Query(params): Query<GetMenusQuery>,
    Extension(state): Extension<AppState>,
    claims: Option<Extension<Claims>>, // auth_layer 中间件注入，可能为 None
) -> Result<Json<Vec<MenuItemResponse>>, AppError> {
    let pool = &state.pool;

    // 提取用户身份
    let is_authenticated = claims.is_some();
    let user_roles: Vec<String> = claims
        .map(|c| c.roles.clone())
        .unwrap_or_default();

    // 加载所有菜单
    let all_menus = MenuService::get_all_menus(pool, &params.product).await?;

    // 构建树
    let tree = MenuService::build_menu_tree(all_menus);

    // 按可见性过滤
    let filtered = MenuService::filter_by_visibility(tree, &user_roles, is_authenticated);

    Ok(Json(filtered))
}

/// GET /v1/menus/admin — 获取完整菜单树（需要管理员）
pub async fn get_all_menus(
    Query(params): Query<GetMenusQuery>,
    Extension(state): Extension<AppState>,
    Extension(claims): Extension<Claims>, // require_auth 中间件保证必有
) -> Result<Json<Vec<MenuItemResponse>>, AppError> {
    let pool = &state.pool;

    // 管理员检查
    if !claims.roles.contains(&"admin".to_string()) {
        return Err(AppError::Forbidden("需要管理员权限".into()));
    }

    let all_menus = MenuService::get_all_menus(pool, &params.product).await?;
    let tree = MenuService::build_menu_tree(all_menus);

    Ok(Json(tree))
}

/// POST /v1/menus/admin — 创建菜单项（管理员）
#[derive(Deserialize)]
pub struct CreateMenuBody {
    #[serde(default)]
    pub parent_id: Option<i64>,
    pub title: String,
    pub path: String,
    pub icon: Option<String>,
    pub component: Option<String>,
    pub product: String,
    #[serde(default = "default_visible_to")]
    pub visible_to: String,
    pub required_permission: Option<String>,
    #[serde(default)]
    pub sort_order: i32,
    pub meta: Option<serde_json::Value>,
}

fn default_visible_to() -> String {
    "public".to_string()
}

pub async fn create_menu(
    Extension(state): Extension<AppState>,
    Extension(claims): Extension<Claims>,
    Json(body): Json<CreateMenuBody>,
) -> Result<(StatusCode, Json<serde_json::Value>), AppError> {
    let pool = &state.pool;

    if !claims.roles.contains(&"admin".to_string()) {
        return Err(AppError::Forbidden("需要管理员权限".into()));
    }

    let id = MenuService::create_menu(
        pool,
        body.parent_id,
        &body.title,
        &body.path,
        body.icon.as_deref(),
        body.component.as_deref(),
        &body.product,
        &body.visible_to,
        body.required_permission.as_deref(),
        body.sort_order,
        body.meta.as_ref(),
    )
    .await?;

    Ok((
        StatusCode::CREATED,
        Json(serde_json::json!({"id": id, "message": "菜单创建成功"})),
    ))
}
