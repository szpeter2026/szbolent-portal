use casbin::{Adapter, Filter, Model};
use sqlx::MySqlPool;
use std::sync::Arc;
use tokio::sync::RwLock;

/// Casbin 引擎封装
pub type SharedEnforcer = Arc<RwLock<Enforcer>>;

// ============================================================
// 自定义 Casbin MySQL Adapter
// ============================================================

/// 数据库中的策略行结构
#[derive(Debug, sqlx::FromRow)]
struct PolicyRow {
    p_type: String,
    v0: String,
    v1: String,
    v2: String,
    v3: String,
    v4: String,
    v5: String,
}

/// 基于 MySQL 的 Casbin 策略适配器
struct MySqlAdapter {
    pool: MySqlPool,
    filtered: bool,
}

impl MySqlAdapter {
    fn new(pool: MySqlPool) -> Self {
        Self {
            pool,
            filtered: false,
        }
    }
}

/// 补齐 Vec 到 6 个元素
fn pad6(rule: &[String]) -> Vec<String> {
    let mut v: Vec<String> = rule.to_vec();
    v.resize(6, String::new());
    v
}

#[async_trait::async_trait]
impl Adapter for MySqlAdapter {
    async fn load_policy(&mut self, m: &mut dyn Model) -> casbin::Result<()> {
        let rows = sqlx::query_as::<_, PolicyRow>(
            "SELECT p_type, v0, v1, v2, v3, v4, v5 FROM casbin_rule",
        )
        .fetch_all(&self.pool)
        .await
        .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;

        for row in rows {
            let rule = vec![row.v0, row.v1, row.v2, row.v3, row.v4, row.v5];
            m.add_policy(&row.p_type, &row.p_type, rule);
        }

        self.filtered = false;
        Ok(())
    }

    async fn load_filtered_policy<'a>(
        &mut self,
        m: &mut dyn Model,
        _f: Filter<'a>,
    ) -> casbin::Result<()> {
        // 简化实现：加载全部策略
        // 对于支持过滤的场景，根据 f.p 和 f.g 参数过滤 SQL
        self.load_policy(m).await?;
        self.filtered = true;
        Ok(())
    }

    async fn save_policy(&mut self, m: &mut dyn Model) -> casbin::Result<()> {
        // 全量保存：清空 → 重新写入
        sqlx::query("DELETE FROM casbin_rule")
            .execute(&self.pool)
            .await
            .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;

        // 重新加载 p 和 g 类型的规则
        for ptype in &["p", "g"] {
            let policies = m.get_policy(ptype, ptype);
            for rule in policies {
                let v = pad6(&rule);
                sqlx::query(
                    "INSERT INTO casbin_rule (p_type, v0, v1, v2, v3, v4, v5) VALUES (?, ?, ?, ?, ?, ?, ?)",
                )
                .bind(ptype)
                .bind(&v[0]).bind(&v[1]).bind(&v[2])
                .bind(&v[3]).bind(&v[4]).bind(&v[5])
                .execute(&self.pool)
                .await
                .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;
            }
        }

        self.filtered = false;
        Ok(())
    }

    async fn clear_policy(&mut self) -> casbin::Result<()> {
        sqlx::query("DELETE FROM casbin_rule")
            .execute(&self.pool)
            .await
            .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;

        self.filtered = false;
        Ok(())
    }

    fn is_filtered(&self) -> bool {
        self.filtered
    }

    async fn add_policy(
        &mut self,
        _sec: &str,
        ptype: &str,
        rule: Vec<String>,
    ) -> casbin::Result<bool> {
        let v = pad6(&rule);
        sqlx::query(
            "INSERT INTO casbin_rule (p_type, v0, v1, v2, v3, v4, v5) VALUES (?, ?, ?, ?, ?, ?, ?)",
        )
        .bind(ptype)
        .bind(&v[0]).bind(&v[1]).bind(&v[2])
        .bind(&v[3]).bind(&v[4]).bind(&v[5])
        .execute(&self.pool)
        .await
        .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;

        Ok(true)
    }

    async fn add_policies(
        &mut self,
        _sec: &str,
        ptype: &str,
        rules: Vec<Vec<String>>,
    ) -> casbin::Result<bool> {
        for rule in rules {
            self.add_policy(_sec, ptype, rule).await?;
        }
        Ok(true)
    }

    async fn remove_policy(
        &mut self,
        _sec: &str,
        ptype: &str,
        rule: Vec<String>,
    ) -> casbin::Result<bool> {
        let v = pad6(&rule);
        sqlx::query(
            "DELETE FROM casbin_rule WHERE p_type = ? AND v0 = ? AND v1 = ? AND v2 = ? AND v3 = ? AND v4 = ? AND v5 = ? LIMIT 1",
        )
        .bind(ptype)
        .bind(&v[0]).bind(&v[1]).bind(&v[2])
        .bind(&v[3]).bind(&v[4]).bind(&v[5])
        .execute(&self.pool)
        .await
        .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;

        Ok(true)
    }

    async fn remove_policies(
        &mut self,
        _sec: &str,
        ptype: &str,
        rules: Vec<Vec<String>>,
    ) -> casbin::Result<bool> {
        for rule in rules {
            self.remove_policy(_sec, ptype, rule).await?;
        }
        Ok(true)
    }

    async fn remove_filtered_policy(
        &mut self,
        _sec: &str,
        ptype: &str,
        field_index: usize,
        field_values: Vec<String>,
    ) -> casbin::Result<bool> {
        let columns = ["v0", "v1", "v2", "v3", "v4", "v5"];
        if field_index >= columns.len() || field_values.is_empty() {
            return Ok(false);
        }

        let query = format!(
            "DELETE FROM casbin_rule WHERE p_type = ? AND {} = ?",
            columns[field_index]
        );

        sqlx::query(&query)
            .bind(ptype)
            .bind(&field_values[0])
            .execute(&self.pool)
            .await
            .map_err(|e| casbin::error::AdapterError(Box::new(e)))?;

        Ok(true)
    }
}

// ============================================================
// 引擎创建和公共接口
// ============================================================

use casbin::prelude::*;

/// 从数据库创建 Casbin 引擎
pub async fn create_casbin_enforcer(
    pool: &MySqlPool,
    _database_url: &str,
) -> anyhow::Result<SharedEnforcer> {
    let model = DefaultModel::from_file("model.conf").await?;
    let adapter = MySqlAdapter::new(pool.clone());
    let enforcer = Enforcer::new(model, adapter).await?;

    tracing::info!(
        "Casbin enforcer initialized with {} policies",
        enforcer.get_policy().len()
    );

    Ok(Arc::new(RwLock::new(enforcer)))
}

/// 检查权限
pub async fn check_permission(
    enforcer: &SharedEnforcer,
    subject: &str,
    resource: &str,
    action: &str,
) -> bool {
    enforcer
        .write()
        .await
        .enforce((subject, resource, action))
        .unwrap_or(false)
}

/// 获取用户的所有 Casbin 角色名
pub async fn get_roles_for_user(enforcer: &SharedEnforcer, user_id: &str) -> Vec<String> {
    enforcer.read().await.get_roles_for_user(user_id, None)
}

/// 列出所有 policy
#[allow(dead_code)]
pub async fn list_policies(enforcer: &SharedEnforcer) -> Vec<Vec<String>> {
    enforcer.read().await.get_policy()
}

/// 添加策略并持久化
#[allow(dead_code)]
pub async fn add_policy(
    enforcer: &SharedEnforcer,
    sub: &str,
    obj: &str,
    act: &str,
) -> anyhow::Result<bool> {
    let result = enforcer
        .write()
        .await
        .add_policy(vec![sub.to_string(), obj.to_string(), act.to_string()])
        .await?;
    Ok(result)
}
