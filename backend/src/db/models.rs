use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use sqlx::FromRow;

// ============================================================
// 菜单/路由模型 — 对应 share_menus 表
// ============================================================

#[derive(Debug, Clone, FromRow, Serialize, Deserialize)]
pub struct MenuRow {
    pub id: i64,
    pub parent_id: Option<i64>,
    pub title: String,
    pub path: String,
    pub icon: Option<String>,
    pub component: Option<String>,
    pub product: String,
    pub visible_to: String,
    pub required_permission: Option<String>,
    pub sort_order: i32,
    pub meta: Option<serde_json::Value>,
    pub status: i8,
    pub created_at: DateTime<Utc>,
    pub updated_at: DateTime<Utc>,
}

// ============================================================
// 页面 Schema 模型 — 对应 share_pages 表
// ============================================================

#[derive(Debug, Clone, FromRow, Serialize, Deserialize)]
pub struct PageRow {
    pub id: String,
    pub product: String,
    pub path: String,
    pub page_type: String,
    pub layout: String,
    pub title: String,
    pub description: Option<String>,
    pub visibility: String,
    pub data_source: Option<String>,
    pub components: serde_json::Value,
    pub meta: Option<serde_json::Value>,
    pub status: String,
    pub created_at: DateTime<Utc>,
    pub updated_at: DateTime<Utc>,
}

// ============================================================
// Casbin 策略模型 — 对应 casbin_rule 表
// ============================================================

#[derive(Debug, Clone, FromRow, Serialize, Deserialize)]
pub struct CasbinRuleRow {
    pub id: Option<i64>,
    pub p_type: String,
    pub v0: String,
    pub v1: String,
    pub v2: String,
    pub v3: String,
    pub v4: String,
    pub v5: String,
}

// ============================================================
// 用户角色映射 — 对应 share_user_roles 表
// ============================================================

#[allow(dead_code)]
#[derive(Debug, Clone, FromRow, Serialize, Deserialize)]
pub struct UserRoleRow {
    pub id: i64,
    pub user_id: String,
    pub product: String,
    pub role: String,
    pub created_at: DateTime<Utc>,}

// ============================================================
// API 响应类型（与前端契约对齐）
// ============================================================

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct MenuItemResponse {
    pub id: i64,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub parent_id: Option<i64>,
    pub title: String,
    pub path: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub icon: Option<String>,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub component: Option<String>,
    pub visible_to: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub required_permission: Option<String>,
    pub sort_order: i32,
    pub product: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub meta: Option<serde_json::Value>,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub children: Option<Vec<MenuItemResponse>>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct PageComponentResponse {
    pub id: String,
    #[serde(rename = "type")]
    pub component_type: String,
    pub props: serde_json::Value,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub data_source: Option<String>,
    #[serde(rename = "visibleTo")]
    pub visible_to: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    #[serde(rename = "requiredRole")]
    pub required_role: Option<String>,
    #[serde(skip_serializing_if = "Option::is_none")]
    #[serde(rename = "requiredVC")]
    pub required_vc: Option<String>,
    pub order: i32,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct PageSchemaResponse {
    pub id: String,
    pub product: String,
    pub path: String,
    #[serde(rename = "pageType")]
    pub page_type: String,
    pub layout: String,
    pub title: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub description: Option<String>,
    pub visibility: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    #[serde(rename = "dataSource")]
    pub data_source: Option<String>,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub meta: Option<serde_json::Value>,
    pub components: Vec<PageComponentResponse>,
    pub status: String,
    #[serde(rename = "createdAt")]
    pub created_at: DateTime<Utc>,    #[serde(rename = "updatedAt")]
    pub updated_at: DateTime<Utc>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct PaginatedResponse<T: Serialize> {
    pub data: Vec<T>,
    pub total: i64,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct PermissionPolicyResponse {
    #[allow(dead_code)]
    pub product: String,
    pub subject: String,
    pub action: String,
    pub resource: String,
}

/// 权限检查请求
#[derive(Debug, Clone, Deserialize)]
pub struct PermissionCheckRequest {
    pub resource: String,
    pub action: String,
}

/// 权限检查响应
#[derive(Debug, Clone, Serialize)]
pub struct PermissionCheckResponse {
    pub allowed: bool,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub reason: Option<String>,
}
