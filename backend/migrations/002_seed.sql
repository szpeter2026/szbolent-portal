-- ============================================================
-- Page Engine Service — Seed Data
-- 种子数据：默认菜单 + Casbin 策略 + 示例页面
-- ============================================================

-- ----------------------------
-- 菜单种子
-- ----------------------------
INSERT INTO share_menus (id, parent_id, title, path, icon, component, product, visible_to, sort_order) VALUES
-- szbolent 门户
(1,  NULL, '首页',     '/',           'home',      NULL, 'szbolent', 'public',  0),
(2,  NULL, '服务',     '/services',   'grid',      NULL, 'szbolent', 'public',  1),
(3,  NULL, '案例',     '/cases',      'folder',    NULL, 'szbolent', 'public',  2),
(4,  NULL, '关于',     '/about',      'info',      NULL, 'szbolent', 'public',  3),
(5,  NULL, '联系',     '/contact',    'mail',      NULL, 'szbolent', 'public',  4),
-- 博客
(6,  NULL, '博客',     '/blog',       'edit',      NULL, 'blog',     'public',  0),
(7,  NULL, '关于我',   '/blog/about', 'user',      NULL, 'blog',     'public',  1),
(8,  NULL, '简历',     '/blog/resume','file-text',  NULL, 'blog',     'authenticated', 2),
-- JobFirst
(9,  NULL, '职位搜索', '/jobs',       'search',    NULL, 'jobfirst', 'public',  0),
(10, NULL, '我的简历', '/resume',     'user',      NULL, 'jobfirst', 'authenticated', 1),
(11, NULL, '投递记录', '/applications','list',     NULL, 'jobfirst', 'authenticated', 2),
(12, NULL, '收件箱',   '/inbox',      'inbox',     NULL, 'jobfirst', 'authenticated', 3),
(13, NULL, '企业面板', '/employer',   'briefcase', NULL, 'jobfirst', 'employer', 4)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ----------------------------
-- Casbin 策略种子
-- ----------------------------
INSERT INTO casbin_rule (p_type, v0, v1, v2) VALUES
-- 策略 (p)
('p', 'visitor',       'public:pages',   'read'),
('p', 'visitor',       'public:blog',    'read'),
('p', 'authenticated', 'auth:pages',     'read'),
('p', 'authenticated', 'auth:blog',      'read'),
('p', 'authenticated', 'resume.view',    'read'),
('p', 'employer',      'resume.detail',  'read'),
('p', 'employer',      'talent.search',  'read'),
('p', 'admin',         '*',              '*'),
-- 角色继承 (g)
('g', 'subscriber',  'authenticated', ''),
('g', 'friend',      'subscriber',    ''),
('g', 'employer',    'authenticated', ''),
('g', 'vc_verified', 'authenticated', ''),
('g', 'admin',       'employer',      '')
ON DUPLICATE KEY UPDATE v2 = VALUES(v2);

-- ----------------------------
-- 示例页面 — 简历 (blog)
-- ----------------------------
INSERT INTO share_pages (id, product, path, page_type, layout, title, description, visibility, components, status)
VALUES (
    'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
    'blog',
    '/blog/resume',
    'detail',
    'sidebar',
    '张三的简历',
    '全栈工程师 — Rust / Vue / TypeScript',
    'public',
    '[
        {"id":"hero-01","type":"Hero","props":{"title":"张三","subtitle":"全栈工程师","avatar":"/avatars/zhangsan.png"},"visibleTo":"public","order":0},
        {"id":"skills-01","type":"Skills","props":{"title":"技术栈","items":[{"name":"Rust","level":85},{"name":"Vue 3","level":90},{"name":"TypeScript","level":88}]},"visibleTo":"public","order":1},
        {"id":"timeline-01","type":"Timeline","props":{"title":"工作经历"},"dataSource":"/v1/blog/resume/zhangsan/experience","visibleTo":"authenticated","order":2},
        {"id":"contact-01","type":"Contact","props":{"title":"联系方式"},"dataSource":"/v1/blog/resume/zhangsan/contact","visibleTo":"employer","order":3}
    ]',
    'published'
) ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ----------------------------
-- 示例页面 — JobFirst 欢迎页
-- ----------------------------
INSERT INTO share_pages (id, product, path, page_type, layout, title, description, visibility, components, status)
VALUES (
    'b2c3d4e5-f6a7-8901-bcde-f12345678901',
    'jobfirst',
    '/',
    'landing',
    'fullWidth',
    'JobFirst — 你的求职 AI 助理',
    '智能匹配、一键投递、实时反馈',
    'public',
    '[
        {"id":"hero-01","type":"Hero","props":{"title":"找到你的下一份工作","subtitle":"AI 驱动的智能求职平台","ctaText":"开始求职","ctaLink":"/jobs"},"visibleTo":"public","order":0},
        {"id":"stats-01","type":"Stats","props":{"title":"平台数据","items":[{"label":"合作企业","value":"2,000+"},{"label":"在招岗位","value":"15,000+"},{"label":"求职者","value":"100,000+"}]},"visibleTo":"public","order":1},
        {"id":"card-01","type":"Card","props":{"title":"为什么选择 JobFirst","cards":[{"title":"AI 精准匹配","desc":"基于你的简历自动推荐最适合的岗位"},{"title":"一键投递","desc":"完善简历后，一键投递心仪职位"},{"title":"实时反馈","desc":"追踪投递状态，不错过任何机会"}]},"visibleTo":"public","order":2}
    ]',
    'published'
) ON DUPLICATE KEY UPDATE title = VALUES(title);
