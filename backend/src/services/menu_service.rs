use sqlx::MySqlPool;

use crate::db::models::{MenuItemResponse, MenuRow};
use crate::error::{AppError, AppResult};

/// 菜单服务 — 菜单树的查询、构建、权限过滤
pub struct MenuService;

impl MenuService {
    /// 获取某个产品的所有活跃菜单（平整列表）
    pub async fn get_all_menus(
        pool: &MySqlPool,
        product: &str,
    ) -> AppResult<Vec<MenuRow>> {
        let menus = sqlx::query_as::<_, MenuRow>(
            r#"
            SELECT * FROM share_menus
            WHERE product = ? AND status = 1
            ORDER BY sort_order ASC, id ASC
            "#,
        )
        .bind(product)
        .fetch_all(pool)
        .await?;

        Ok(menus)
    }

    /// 将平整的菜单列表构建为树形结构
    pub fn build_menu_tree(menus: Vec<MenuRow>) -> Vec<MenuItemResponse> {
        let items: Vec<MenuItemResponse> = menus
            .iter()
            .map(|m| MenuItemResponse {
                id: m.id,
                parent_id: m.parent_id,
                title: m.title.clone(),
                path: m.path.clone(),
                icon: m.icon.clone(),
                component: m.component.clone(),
                visible_to: m.visible_to.clone(),
                required_permission: m.required_permission.clone(),
                sort_order: m.sort_order,
                product: m.product.clone(),
                meta: m.meta.clone(),
                children: None,
            })
            .collect();

        let mut roots: Vec<MenuItemResponse> = Vec::new();
        for item in &items {
            if item.parent_id.is_none() {
                roots.push(item.clone());
            }
        }

        fn attach_children(
            parent: &mut MenuItemResponse,
            all: &[MenuItemResponse],
        ) {
            let children: Vec<MenuItemResponse> = all
                .iter()
                .filter(|m| m.parent_id == Some(parent.id))
                .cloned()
                .collect();

            if !children.is_empty() {
                let mut children = children;
                for child in &mut children {
                    attach_children(child, all);
                }
                parent.children = Some(children);
            }
        }

        for root in &mut roots {
            attach_children(root, &items);
        }

        roots
    }

    /// 按用户可见性过滤菜单树
    ///
    /// 过滤规则：
    /// - `public`: 始终保留
    /// - `authenticated`: 用户已登录则保留
    /// - 其他角色 (`subscriber`, `employer`, `admin`, `vc_verified`):
    ///   检查用户角色 + required_permission 是否满足
    pub fn filter_by_visibility(
        tree: Vec<MenuItemResponse>,
        user_roles: &[String],
        is_authenticated: bool,
    ) -> Vec<MenuItemResponse> {
        tree.into_iter()
            .filter_map(|mut item| {
                let visible = match item.visible_to.as_str() {
                    "public" => true,
                    "authenticated" => is_authenticated,
                    role => user_roles.contains(&role.to_string()),
                };

                if !visible {
                    return None;
                }

                // 递归过滤子菜单
                if let Some(children) = item.children.take() {
                    let filtered = Self::filter_by_visibility(
                        children,
                        user_roles,
                        is_authenticated,
                    );
                    if !filtered.is_empty() {
                        item.children = Some(filtered);
                    }
                }

                Some(item)
            })
            .collect()
    }

    /// 创建菜单项（管理员）
    pub async fn create_menu(
        pool: &MySqlPool,
        parent_id: Option<i64>,
        title: &str,
        path: &str,
        icon: Option<&str>,
        component: Option<&str>,
        product: &str,
        visible_to: &str,
        required_permission: Option<&str>,
        sort_order: i32,
        meta: Option<&serde_json::Value>,
    ) -> AppResult<i64> {
        let result = sqlx::query(
            r#"
            INSERT INTO share_menus (parent_id, title, path, icon, component, product, visible_to, required_permission, sort_order, meta)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            "#,
        )
        .bind(parent_id)
        .bind(title)
        .bind(path)
        .bind(icon)
        .bind(component)
        .bind(product)
        .bind(visible_to)
        .bind(required_permission)
        .bind(sort_order)
        .bind(meta)
        .execute(pool)
        .await
        .map_err(|e| AppError::Database(e))?;

        Ok(result.last_insert_id() as i64)
    }
}
