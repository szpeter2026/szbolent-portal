-- ============================================================
-- Page Engine Service — Extended Seed Data
-- 扩展 szbolent 产品菜单 + 路由组件 + 页面 Schema
-- ============================================================

-- ----------------------------
-- 修正已有菜单：补 component 字段（路由注入必需）
-- ----------------------------
UPDATE share_menus SET component = 'Home'     WHERE product = 'szbolent' AND path = '/';
UPDATE share_menus SET component = 'Services' WHERE product = 'szbolent' AND path = '/services';
UPDATE share_menus SET component = 'About'    WHERE product = 'szbolent' AND path = '/about';
UPDATE share_menus SET component = 'Contact'  WHERE product = 'szbolent' AND path = '/contact';

-- 案例：path 从 /cases 改为 /case-study（与静态路由对齐），补 component
UPDATE share_menus SET path = '/case-study', component = 'CaseStudy' WHERE product = 'szbolent' AND path = '/cases';

-- ----------------------------
-- 新增菜单：博客 / 加入我们（szbolent 产品）
-- ----------------------------
INSERT INTO share_menus (id, parent_id, title, path, icon, component, product, visible_to, sort_order) VALUES
(14, NULL, '博客',     '/blog',    'edit',  'Blog',    'szbolent', 'public', 5),
(15, NULL, '加入我们', '/careers', 'users', 'Careers', 'szbolent', 'public', 6)
ON DUPLICATE KEY UPDATE title = VALUES(title), component = VALUES(component);

-- ----------------------------
-- 页面 Schema：博客列表页
-- ----------------------------
INSERT INTO share_pages (id, product, path, page_type, layout, title, description, visibility, components, status)
VALUES (
    'c3d4e5f6-a7b8-9012-cdef-123456789012',
    'szbolent',
    '/blog',
    'list',
    'default',
    '博客',
    '技术分享与行业洞察',
    'public',
    '[
        {"id":"blog-hero","type":"Hero","props":{"title":"博客","subtitle":"技术分享与行业洞察"},"visibleTo":"public","order":0}
    ]',
    'published'
) ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ----------------------------
-- 页面 Schema：招聘列表页
-- ----------------------------
INSERT INTO share_pages (id, product, path, page_type, layout, title, description, visibility, components, status)
VALUES (
    'd4e5f6a7-b8c9-0123-defa-234567890123',
    'szbolent',
    '/careers',
    'list',
    'default',
    '加入我们',
    '与我们一起创造未来',
    'public',
    '[
        {"id":"careers-hero","type":"Hero","props":{"title":"加入我们","subtitle":"与我们一起创造未来"},"visibleTo":"public","order":0}
    ]',
    'published'
) ON DUPLICATE KEY UPDATE title = VALUES(title);
