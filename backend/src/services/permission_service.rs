use crate::auth::casbin_ext::{check_permission, get_roles_for_user, SharedEnforcer};
use crate::db::models::{CasbinRuleRow, PermissionPolicyResponse};
use crate::error::AppResult;
use sqlx::MySqlPool;

/// 权限服务 — Casbin 策略管理和权限检查
pub struct PermissionService;

impl PermissionService {
    /// 检查用户是否有某权限
    pub async fn check(
        enforcer: &SharedEnforcer,
        user_id: &str,
        resource: &str,
        action: &str,
    ) -> AppResult<bool> {
        Ok(check_permission(enforcer, user_id, resource, action).await)
    }

    /// 获取用户的所有 Casbin 策略
    pub async fn get_user_policies(
        pool: &MySqlPool,
        enforcer: &SharedEnforcer,
        user_id: &str,
    ) -> AppResult<Vec<PermissionPolicyResponse>> {
        // 从 Casbin 获取用户所有角色
        let roles = get_roles_for_user(enforcer, user_id).await;

        // 查询 Casbin 策略表中与这些角色相关的策略
        let mut all_roles = roles.clone();
        all_roles.push(user_id.to_string());

        let placeholders = all_roles
            .iter()
            .map(|_| "?")
            .collect::<Vec<_>>()
            .join(",");

        let query = format!(
            "SELECT * FROM casbin_rule WHERE p_type = 'p' AND v0 IN ({})",
            placeholders
        );

        let mut q = sqlx::query_as::<_, CasbinRuleRow>(&query);
        for role in &all_roles {
            q = q.bind(role);
        }

        let rules = q.fetch_all(pool).await?;

        let mut policies: Vec<PermissionPolicyResponse> = rules
            .into_iter()
            .map(|r| PermissionPolicyResponse {
                product: "global".to_string(),
                subject: r.v0,
                action: r.v2,
                resource: r.v1,
            })
            .collect();

        // 去重
        policies.sort_by(|a, b| {
            a.resource
                .cmp(&b.resource)
                .then(a.action.cmp(&b.action))
        });
        policies.dedup_by(|a, b| {
            a.subject == b.subject && a.resource == b.resource && a.action == b.action
        });

        Ok(policies)
    }

    /// 获取用户所有角色
    #[allow(dead_code)]
    pub async fn get_user_roles(
        enforcer: &SharedEnforcer,
        user_id: &str,
    ) -> AppResult<Vec<String>> {
        Ok(get_roles_for_user(enforcer, user_id).await)
    }
}
