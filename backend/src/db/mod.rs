use sqlx::mysql::MySqlPool;
use sqlx::migrate::Migrator;
use std::path::Path;

pub mod models;

/// 创建 MySQL 连接池
pub async fn create_pool(database_url: &str) -> anyhow::Result<MySqlPool> {
    let pool = MySqlPool::connect(database_url).await?;
    tracing::info!("Database connected");
    Ok(pool)
}

/// 运行数据库迁移
pub async fn run_migrations(pool: &MySqlPool) -> anyhow::Result<()> {
    let migrations_path = Path::new("migrations");
    if !migrations_path.exists() {
        tracing::warn!("Migrations directory not found at {:?}, skipping", migrations_path);
        // 回退到项目根目录查找
        let alt_path = Path::new("../database/share_core_schema.sql");
        if alt_path.exists() {
            tracing::info!("Found schema at {:?}", alt_path);
            let sql = std::fs::read_to_string(alt_path)?;
            // 按分号分割并执行（简单实现）
            for statement in sql.split(';') {
                let trimmed = statement.trim();
                if !trimmed.is_empty() && !trimmed.starts_with("--") {
                    sqlx::query(trimmed).execute(pool).await?;
                }
            }
            tracing::info!("Migration completed from share_core_schema.sql");
        }
        return Ok(());
    }

    let m = Migrator::new(migrations_path).await?;
    m.run(pool).await?;
    tracing::info!("Database migrations completed");
    Ok(())
}

/// 健康检查
#[allow(dead_code)]
pub async fn health_check(pool: &MySqlPool) -> bool {
    sqlx::query("SELECT 1").execute(pool).await.is_ok()
}
