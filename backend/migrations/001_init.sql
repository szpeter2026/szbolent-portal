-- ============================================================
-- Page Engine Service — Database Migration
-- 与 ../database/share_core_schema.sql 等价，用于 Rust sqlx migrate
-- ============================================================

-- 1. 菜单/路由表
CREATE TABLE IF NOT EXISTS share_menus (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    parent_id           BIGINT       DEFAULT NULL,
    title               VARCHAR(100) NOT NULL,
    path                VARCHAR(255) NOT NULL,
    icon                VARCHAR(50)  DEFAULT NULL,
    component           VARCHAR(100) DEFAULT NULL,
    product             VARCHAR(50)  NOT NULL DEFAULT 'szbolent',
    visible_to          VARCHAR(30)  NOT NULL DEFAULT 'public',
    required_permission VARCHAR(100) DEFAULT NULL,
    sort_order          INT          NOT NULL DEFAULT 0,
    meta                JSON         DEFAULT NULL,
    status              TINYINT      NOT NULL DEFAULT 1,
    created_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_product  (product),
    INDEX idx_parent   (parent_id),
    INDEX idx_path     (path),
    FOREIGN KEY (parent_id) REFERENCES share_menus(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. 页面 Schema 表
CREATE TABLE IF NOT EXISTS share_pages (
    id          CHAR(36)     PRIMARY KEY,
    product     VARCHAR(50)  NOT NULL DEFAULT 'szbolent',
    path        VARCHAR(255) NOT NULL,
    page_type   VARCHAR(30)  NOT NULL DEFAULT 'custom',
    layout      VARCHAR(30)  NOT NULL DEFAULT 'default',
    title       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    visibility  VARCHAR(30)  NOT NULL DEFAULT 'public',
    data_source VARCHAR(512) DEFAULT NULL,
    components  JSON         NOT NULL,
    meta        JSON         DEFAULT NULL,
    status      VARCHAR(20)  NOT NULL DEFAULT 'draft',
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX uq_product_path (product, path),
    INDEX idx_product (product),
    INDEX idx_status  (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Casbin 策略表
CREATE TABLE IF NOT EXISTS casbin_rule (
    id      BIGINT PRIMARY KEY AUTO_INCREMENT,
    p_type  VARCHAR(10)  NOT NULL,
    v0      VARCHAR(256) DEFAULT '',
    v1      VARCHAR(256) DEFAULT '',
    v2      VARCHAR(256) DEFAULT '',
    v3      VARCHAR(256) DEFAULT '',
    v4      VARCHAR(256) DEFAULT '',
    v5      VARCHAR(256) DEFAULT '',

    INDEX idx_p_type (p_type),
    INDEX idx_v0_v1  (v0, v1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. 用户角色映射表
CREATE TABLE IF NOT EXISTS share_user_roles (
    id         BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id    VARCHAR(128) NOT NULL,
    product    VARCHAR(50)  NOT NULL DEFAULT 'szbolent',
    role       VARCHAR(50)  NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX uq_user_product_role (user_id, product, role),
    INDEX idx_user    (user_id),
    INDEX idx_product (product)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
