use axum::{middleware, Router};
use std::net::SocketAddr;
use tower_http::cors::{Any, CorsLayer};
use tower_http::trace::TraceLayer;
use tracing::info;

mod auth;
mod config;
mod db;
mod error;
mod handlers;
mod services;

use auth::jwt::{auth_layer, require_auth_layer, JwtSecret};
use config::AppConfig;
use db::create_pool;

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    // 初始化日志
    tracing_subscriber::fmt()
        .with_env_filter(
            tracing_subscriber::EnvFilter::try_from_default_env()
                .unwrap_or_else(|_| "page_engine_service=info".into()),
        )
        .init();

    // 加载配置
    let config = AppConfig::from_env()?;
    info!("Starting Page Engine Service on port {}", config.server_port);

    // 数据库连接池
    let pool = create_pool(&config.database_url).await?;

    // 运行数据库迁移
    db::run_migrations(&pool).await?;

    // Casbin 引擎
    let casbin_enforcer =
        auth::casbin_ext::create_casbin_enforcer(&pool, &config.database_url).await?;

    // CORS
    let cors = CorsLayer::new()
        .allow_origin(Any)
        .allow_methods(Any)
        .allow_headers(Any);

    // 共享状态
    let app_state = handlers::AppState {
        pool: pool.clone(),
        casbin_enforcer: casbin_enforcer.clone(),
        jwt_secret: config.jwt_secret.clone(),
    };

    // 路由
    let app = Router::new()
        // 公开路由 — 可选认证
        .merge(
            handlers::public_routes()
                .layer(middleware::from_fn(auth_layer)),
        )
        // 认证路由 — 强制认证
        .merge(
            handlers::auth_routes()
                .layer(middleware::from_fn(require_auth_layer)),
        )
        .layer(TraceLayer::new_for_http())
        .layer(cors)
        .layer(axum::Extension(app_state))
        .layer(axum::Extension(JwtSecret(config.jwt_secret.clone())));

    let addr: SocketAddr = format!("{}:{}", config.server_host, config.server_port)
        .parse()?;
    info!("Listening on http://{}", addr);

    let listener = tokio::net::TcpListener::bind(addr).await?;
    axum::serve(listener, app).await?;

    Ok(())
}
