use std::env;

/// 应用配置 — 从环境变量加载
#[derive(Clone, Debug)]
pub struct AppConfig {
    pub database_url: String,
    pub jwt_secret: String,
    #[allow(dead_code)]
    pub jwt_expiry_hours: i64,
    pub server_host: String,
    pub server_port: u16,
}

impl AppConfig {
    pub fn from_env() -> anyhow::Result<Self> {
        // 加载 .env 文件（不存在也不报错）
        let _ = dotenvy::dotenv();

        Ok(Self {
            database_url: env::var("DATABASE_URL")
                .unwrap_or_else(|_| "mysql://looma:looma@127.0.0.1:3306/looma".into()),
            jwt_secret: env::var("JWT_SECRET")
                .unwrap_or_else(|_| "dev-secret-change-in-production".into()),
            jwt_expiry_hours: env::var("JWT_EXPIRY_HOURS")
                .ok()
                .and_then(|s| s.parse().ok())
                .unwrap_or(24),
            server_host: env::var("SERVER_HOST").unwrap_or_else(|_| "127.0.0.1".into()),
            server_port: env::var("SERVER_PORT")
                .ok()
                .and_then(|s| s.parse().ok())
                .unwrap_or(5300),
        })
    }
}
