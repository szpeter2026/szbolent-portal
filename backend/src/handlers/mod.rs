use axum::{
    routing::{delete, get, post},
    Router,
};

use crate::auth::casbin_ext::SharedEnforcer;

mod menus;
mod pages;
mod permissions;

/// 应用全局状态
#[derive(Clone)]
pub struct AppState {
    pub pool: sqlx::MySqlPool,
    pub casbin_enforcer: SharedEnforcer,
    #[allow(dead_code)]
    pub jwt_secret: String,
}

/// 公开路由 — 可选认证（auth_layer 中间件处理）
pub fn public_routes() -> Router {
    Router::new()
        .route("/v1/menus", get(menus::get_menus))
        .route("/v1/pages/by-path", get(pages::get_page_by_path))
}

/// 认证路由 — 强制认证（require_auth_layer 中间件处理）
pub fn auth_routes() -> Router {
    Router::new()
        // 菜单管理
        .route(
            "/v1/menus/admin",
            get(menus::get_all_menus).post(menus::create_menu),
        )
        // 页面管理
        .route("/v1/pages", get(pages::list_pages).post(pages::upsert_page))
        .route("/v1/pages/{id}", delete(pages::delete_page))
        // 权限
        .route(
            "/v1/permissions/check",
            post(permissions::check_permission),
        )
        .route("/v1/permissions/my", get(permissions::get_my_permissions))
}
