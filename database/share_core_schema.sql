-- ============================================================
-- share-core 数据库 Schema（参考实现）
--
-- 部署时在 Looma 数据库中执行，目标数据库：looma
-- Rust 迁移时使用 diesel/sea-query/sqlx 重现
-- ============================================================

-- -----------------------------------------------------------
-- 1. 菜单/路由表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS share_menus (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    parent_id           INT          DEFAULT NULL,
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

-- -----------------------------------------------------------
-- 2. 页面 Schema 表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS share_pages (
    id          CHAR(36)     PRIMARY KEY,  -- UUID
    product     VARCHAR(50)  NOT NULL DEFAULT 'szbolent',
    path        VARCHAR(255) NOT NULL,
    page_type   VARCHAR(30)  NOT NULL DEFAULT 'custom',
    layout      VARCHAR(30)  NOT NULL DEFAULT 'default',
    title       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    visibility  VARCHAR(30)  NOT NULL DEFAULT 'public',
    data_source VARCHAR(512) DEFAULT NULL,
    components  JSON         NOT NULL,       -- PageComponent[]
    meta        JSON         DEFAULT NULL,   -- SEO ogImage / ogType / canonical
    status      VARCHAR(20)  NOT NULL DEFAULT 'draft',
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE INDEX uq_product_path (product, path),
    INDEX idx_product (product),
    INDEX idx_status  (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 3. Casbin 策略表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS casbin_rule (
    id      INT PRIMARY KEY AUTO_INCREMENT,
    p_type  VARCHAR(10)  NOT NULL,   -- 'p' = policy, 'g' = role
    v0      VARCHAR(256) DEFAULT '',  -- subject
    v1      VARCHAR(256) DEFAULT '',  -- object / role
    v2      VARCHAR(256) DEFAULT '',  -- action
    v3      VARCHAR(256) DEFAULT '',
    v4      VARCHAR(256) DEFAULT '',
    v5      VARCHAR(256) DEFAULT '',

    INDEX idx_p_type (p_type),
    INDEX idx_v0_v1  (v0, v1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 4. 用户角色表（简化版，对接 YeDall DID）
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS share_user_roles (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id    VARCHAR(128) NOT NULL,      -- YeDall DID subject
    product    VARCHAR(50)  NOT NULL DEFAULT 'szbolent',
    role       VARCHAR(50)  NOT NULL,      -- 'admin' | 'employer' | 'subscriber' | ...
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE INDEX uq_user_product_role (user_id, product, role),
    INDEX idx_user    (user_id),
    INDEX idx_product (product)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 5. 种子数据：默认菜单
-- -----------------------------------------------------------
INSERT INTO share_menus (id, parent_id, title, path, icon, component, product, visible_to, sort_order) VALUES
-- szbolent 门户
(1,  NULL, '首页',     '/',           'home',       NULL, 'szbolent', 'public',  0),
(2,  NULL, '服务',     '/services',   'grid',       NULL, 'szbolent', 'public',  1),
(3,  NULL, '案例',     '/cases',      'folder',     NULL, 'szbolent', 'public',  2),
(4,  NULL, '关于',     '/about',      'info',       NULL, 'szbolent', 'public',  3),
(5,  NULL, '联系',     '/contact',    'mail',       NULL, 'szbolent', 'public',  4),
-- 博客
(6,  NULL, '博客',     '/blog',       'edit',       NULL, 'blog',     'public',  0),
(7,  NULL, '关于我',   '/blog/about', 'user',       NULL, 'blog',     'public',  1),
(8,  NULL, '简历',     '/blog/resume','file-text',  NULL, 'blog',     'authenticated', 2),
-- JobFirst
(9,  NULL, '职位搜索', '/jobs',       'search',     NULL, 'jobfirst', 'public',  0),
(10, NULL, '我的简历', '/resume',     'user',       NULL, 'jobfirst', 'authenticated', 1),
(11, NULL, '投递记录', '/applications','list',      NULL, 'jobfirst', 'authenticated', 2),
(12, NULL, '收件箱',   '/inbox',      'inbox',      NULL, 'jobfirst', 'authenticated', 3),
(13, NULL, '企业面板', '/employer',   'briefcase',  NULL, 'jobfirst', 'employer', 4)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- -----------------------------------------------------------
-- 6. 种子数据：默认 Casbin 策略示例
-- -----------------------------------------------------------
-- 游客 (visitor) 可以读公开资源
INSERT INTO casbin_rule (p_type, v0, v1, v2) VALUES
('p', 'visitor',     'public:pages',   'read'),
('p', 'visitor',     'public:blog',    'read'),
-- 认证用户 (authenticated) 可以读认证内容
('p', 'authenticated', 'auth:pages',   'read'),
('p', 'authenticated', 'auth:blog',    'read'),
('p', 'authenticated', 'resume.view',  'read'),
-- 雇主 (employer) 可以读求职者简历
('p', 'employer',    'resume.detail',  'read'),
('p', 'employer',    'talent.search',  'read'),
-- 管理员全权限
('p', 'admin',       '*',              '*')
ON DUPLICATE KEY UPDATE v2 = VALUES(v2);

-- 角色继承
INSERT INTO casbin_rule (p_type, v0, v1, v2) VALUES
('g', 'subscriber',  'authenticated', ''),
('g', 'friend',      'subscriber',    ''),
('g', 'employer',    'authenticated', ''),
('g', 'vc_verified', 'authenticated', ''),
('g', 'admin',       'employer',      '')
ON DUPLICATE KEY UPDATE v2 = VALUES(v2);
