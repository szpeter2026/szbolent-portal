use sqlx::MySqlPool;
use uuid::Uuid;

use crate::db::models::{PageComponentResponse, PageRow, PageSchemaResponse};
use crate::error::AppResult;

/// 页面服务 — Page Schema 的 CRUD 和权限过滤
pub struct PageService;

impl PageService {
    /// 根据路径获取页面 Schema
    pub async fn get_by_path(
        pool: &MySqlPool,
        path: &str,
    ) -> AppResult<Option<PageRow>> {
        let page = sqlx::query_as::<_, PageRow>(
            r#"
            SELECT * FROM share_pages
            WHERE path = ? AND status = 'published'
            LIMIT 1
            "#,
        )
        .bind(path)
        .fetch_optional(pool)
        .await?;

        Ok(page)
    }

    /// 列出产品的页面（管理员）
    pub async fn list_pages(
        pool: &MySqlPool,
        product: &str,
        status: Option<&str>,
        page: i64,
        limit: i64,
    ) -> AppResult<(Vec<PageRow>, i64)> {
        // 总数
        let total: (i64,) = match status {
            Some(s) => {
                sqlx::query_as(
                    "SELECT COUNT(*) FROM share_pages WHERE product = ? AND status = ?",
                )
                .bind(product)
                .bind(s)
                .fetch_one(pool)
                .await?
            }
            None => {
                sqlx::query_as(
                    "SELECT COUNT(*) FROM share_pages WHERE product = ?",
                )
                .bind(product)
                .fetch_one(pool)
                .await?
            }
        };

        // 列表
        let offset = (page - 1) * limit;
        let pages = match status {
            Some(s) => {
                sqlx::query_as::<_, PageRow>(
                    "SELECT * FROM share_pages WHERE product = ? AND status = ? ORDER BY updated_at DESC LIMIT ? OFFSET ?",
                )
                .bind(product)
                .bind(s)
                .bind(limit)
                .bind(offset)
                .fetch_all(pool)
                .await?
            }
            None => {
                sqlx::query_as::<_, PageRow>(
                    "SELECT * FROM share_pages WHERE product = ? ORDER BY updated_at DESC LIMIT ? OFFSET ?",
                )
                .bind(product)
                .bind(limit)
                .bind(offset)
                .fetch_all(pool)
                .await?
            }
        };

        Ok((pages, total.0))
    }

    /// 创建或更新页面 Schema
    pub async fn upsert(
        pool: &MySqlPool,
        id: Option<String>,
        product: &str,
        path: &str,
        page_type: &str,
        layout: &str,
        title: &str,
        description: Option<&str>,
        visibility: &str,
        data_source: Option<&str>,
        components: &serde_json::Value,
        meta: Option<&serde_json::Value>,
        status: &str,
    ) -> AppResult<String> {
        let page_id = id.unwrap_or_else(|| Uuid::new_v4().to_string());

        sqlx::query(
            r#"
            INSERT INTO share_pages (id, product, path, page_type, layout, title, description, visibility, data_source, components, meta, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                page_type = VALUES(page_type),
                layout = VALUES(layout),
                title = VALUES(title),
                description = VALUES(description),
                visibility = VALUES(visibility),
                data_source = VALUES(data_source),
                components = VALUES(components),
                meta = VALUES(meta),
                status = VALUES(status)
            "#,
        )
        .bind(&page_id)
        .bind(product)
        .bind(path)
        .bind(page_type)
        .bind(layout)
        .bind(title)
        .bind(description)
        .bind(visibility)
        .bind(data_source)
        .bind(components)
        .bind(meta)
        .bind(status)
        .execute(pool)
        .await?;

        Ok(page_id)
    }

    /// 删除页面
    pub async fn delete(pool: &MySqlPool, id: &str) -> AppResult<bool> {
        let result = sqlx::query("DELETE FROM share_pages WHERE id = ?")
            .bind(id)
            .execute(pool)
            .await?;

        Ok(result.rows_affected() > 0)
    }

    /// 将 PageRow 转换为 API 响应格式
    pub fn to_response(page: PageRow) -> PageSchemaResponse {
        // 解析 components JSON 为 Vec<PageComponentResponse>
        let components: Vec<PageComponentResponse> =
            serde_json::from_value(page.components.clone()).unwrap_or_default();

        PageSchemaResponse {
            id: page.id,
            product: page.product,
            path: page.path,
            page_type: page.page_type,
            layout: page.layout,
            title: page.title,
            description: page.description,
            visibility: page.visibility,
            data_source: page.data_source,
            meta: page.meta,
            components,
            status: page.status,
            created_at: page.created_at,
            updated_at: page.updated_at,
        }
    }

    /// 按用户权限过滤组件列表
    ///
    /// 过滤规则：
    /// - `public` → 始终保留
    /// - `authenticated` → 用户已登录
    /// - 其他角色 → 检查用户角色是否匹配
    pub fn filter_components(
        components: Vec<PageComponentResponse>,
        user_roles: &[String],
        is_authenticated: bool,
    ) -> Vec<PageComponentResponse> {
        components
            .into_iter()
            .filter(|comp| match comp.visible_to.as_str() {
                "public" => true,
                "authenticated" => is_authenticated,
                role => user_roles.contains(&role.to_string()),
            })
            .collect()
    }
}
