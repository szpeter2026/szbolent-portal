<?php
/**
 * Bolent Astra Child Theme — functions.php v2.0
 * 项目：szbolent-portal — HarmonyOS 生态服务商
 * 功能：样式加载、CPT 注册、导航菜单、CF7 支持、页面模板
 */

if (!defined('ABSPATH')) exit;

define('BOLENT_VERSION', '2.0.0');
define('BOLENT_DIR', get_stylesheet_directory());
define('BOLENT_URI', get_stylesheet_directory_uri());

/**
 * 1. 样式与字体加载
 */
function bolent_enqueue_styles() {
    // 父主题 Astra 样式
    wp_enqueue_style('astra-parent', get_template_directory_uri() . '/style.css');

    // 子主题品牌样式
    wp_enqueue_style('bolent-child', get_stylesheet_uri(), array('astra-parent'), BOLENT_VERSION);

    // Google Fonts: Manrope + Noto Sans SC + Noto Serif SC
    wp_enqueue_style('bolent-fonts', 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Noto+Sans+SC:wght@400;500;700&family=Noto+Serif+SC:wght@400;500;700&display=swap', array(), null);

    // 移动端菜单脚本
    wp_enqueue_script('bolent-mobile-menu', BOLENT_URI . '/assets/mobile-menu.js', array(), BOLENT_VERSION, true);
}
add_action('wp_enqueue_scripts', 'bolent_enqueue_styles');

/**
 * 2. 注册导航菜单
 */
function bolent_register_menus() {
    register_nav_menus(array(
        'primary' => __('主导航菜单', 'bolent-astra-child'),
        'footer-services' => __('页脚 - 服务', 'bolent-astra-child'),
        'footer-explore' => __('页脚 - 探索', 'bolent-astra-child'),
        'footer-contact' => __('页脚 - 联系', 'bolent-astra-child'),
    ));
}
add_action('init', 'bolent_register_menus');

/**
 * 主导航默认菜单（未设置菜单时显示）
 */
function bolent_default_menu() {
    $items = array(
        '/' => '首页',
        '/about' => '关于我们',
        '/services' => '服务',
        '/case-studies' => '案例',
        '/blog' => '博客',
        '/careers' => '招贤纳士',
    );
    echo '<nav class="bolent-nav">';
    foreach ($items as $slug => $label) {
        $url = home_url($slug);
        $active = is_page(ltrim($slug, '/')) ? ' current-menu-item' : '';
        echo '<a href="' . esc_url($url) . '" class="' . esc_attr($active) . '">' . esc_html($label) . '</a>';
    }
    echo '</nav>';
}

/**
 * 页脚默认菜单
 */
function bolent_footer_services_menu() {
    echo '<ul>';
    $services = array(
        '鸿蒙应用开发' => '/services/harmonyos',
        '软件开发' => '/services/development',
        '数字化 & 数据' => '/services/digital',
        '自动化 & QA' => '/services/automation',
        'IT 外包' => '/services/outsourcing',
    );
    foreach ($services as $label => $slug) {
        echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}

function bolent_footer_explore_menu() {
    echo '<ul>';
    $explore = array(
        '成功案例' => '/case-studies',
        '技术博客' => '/blog',
        '关于我们' => '/about',
        '招贤纳士' => '/careers',
    );
    foreach ($explore as $label => $slug) {
        echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}

function bolent_footer_contact_menu() {
    echo '<ul>';
    $contact = array(
        '联系我们' => '/contact',
        '合作伙伴' => '/partners',
    );
    foreach ($contact as $label => $slug) {
        echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * 3. 加载 Custom Post Type 配置
 */
require_once BOLENT_DIR . '/cpt/services.php';
require_once BOLENT_DIR . '/cpt/careers.php';
require_once BOLENT_DIR . '/cpt/case-studies.php';

/**
 * 4. Astra Customizer 覆盖
 * 将 Astra 默认配色替换为 HarmonyOS 品牌色
 */
function bolent_astra_customizer_defaults($wp_customize) {
    if (function_exists('astra_get_option')) {
        $wp_customize->get_setting('astra-settings[link-color]')->default = '#1A73E8';
        $wp_customize->get_setting('astra-settings[text-color]')->default = '#1F2440';
        $wp_customize->get_setting('astra-settings[heading-color]')->default = '#0A0E27';
    }
}
add_action('customize_register', 'bolent_astra_customizer_defaults', 20);

/**
 * 5. 页面模板注册 — 7 个模板
 */
function bolent_page_templates($templates) {
    $templates['page-templates/page-home.php'] = __('Bolent 首页', 'bolent-astra-child');
    $templates['page-templates/page-about.php'] = __('Bolent 关于我们', 'bolent-astra-child');
    $templates['page-templates/page-services.php'] = __('Bolent 服务列表', 'bolent-astra-child');
    $templates['page-templates/page-contact.php'] = __('Bolent 联系我们', 'bolent-astra-child');
    $templates['page-templates/page-careers.php'] = __('Bolent 招贤纳士', 'bolent-astra-child');
    $templates['page-templates/page-case-studies.php'] = __('Bolent 案例研究', 'bolent-astra-child');
    $templates['page-templates/page-blog.php'] = __('Bolent 博客', 'bolent-astra-child');
    return $templates;
}
add_filter('theme_page_templates', 'bolent_page_templates');

/**
 * 6. Contact Form 7 样式覆盖
 */
function bolent_cf7_styles() {
    if (function_exists('wpcf7_enqueue_styles')) {
        wp_dequeue_style('contact-form-7');
    }
}
add_action('wp_enqueue_scripts', 'bolent_cf7_styles', 99);

/**
 * 7. 禁用 Astra 默认 Header/Footer（使用自定义模板）
 */
function bolent_disable_astra_header() {
    remove_action('astra_header', 'astra_header_markup');
    remove_action('astra_footer', 'astra_footer_markup');
}
add_action('wp_loaded', 'bolent_disable_astra_header');

/**
 * 8. 自定义 excerpt 长度
 */
function bolent_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'bolent_excerpt_length');

function bolent_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'bolent_excerpt_more');

/**
 * 9. 预设示例内容激活钩子
 * 修复路径：从旧版 acf-fields/ 改为主题根目录
 */
function bolent_import_demo_content() {
    if (get_option('bolent_demo_imported') !== 'yes') {
        $demo_file = BOLENT_DIR . '/demo-content.php';
        if (file_exists($demo_file)) {
            require_once $demo_file;
        }
        update_option('bolent_demo_imported', 'yes');
    }
}
add_action('after_switch_theme', 'bolent_import_demo_content');

/**
 * 10. SVG 图标辅助函数
 * 供各页面模板调用，替代旧版 emoji 图标
 */
if (!function_exists('bolent_get_svg_icon')) {
    function bolent_get_svg_icon($name, $color = 'currentColor') {
        $icons = array(
            'smartphone' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
            'cpu' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>',
            'atom' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><circle cx="12" cy="12" r="1"/><path d="M20.2 20.2c2.04-2.03.02-7.36-4.5-11.9-4.54-4.52-9.87-6.54-11.9-4.5-2.04 2.03-.02 7.36 4.5 11.9 4.54 4.52 9.87 6.54 11.9 4.5z"/><path d="M15.7 15.7c4.52-4.54 6.54-9.87 4.5-11.9-2.03-2.04-7.36-.02-11.9 4.5-4.52 4.54-6.54 9.87-4.5 11.9 2.03 2.04 7.36.02 11.9-4.5z"/></svg>',
            'package' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
            'gitmerge' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 0 0 9 9"/></svg>',
            'layers' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
            'database' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
            'code' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
            'bot' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg>',
            'settings' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
            'rocket' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
            'globe' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
            'target' => '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
            'sparkles' => '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3L12 3z"/></svg>',
            'wrench' => '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
            'heart' => '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
            'cloud' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1"><path d="M17.5 19a4.5 4.5 0 1 0 0-9h-1.8A7 7 0 1 0 4 14"/></svg>',
            'shopping' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="1"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
            'mail' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
            'phone' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
            'map' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
            'arrow-right' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr($color) . '" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>',
        );
        return isset($icons[$name]) ? $icons[$name] : $icons['layers'];
    }
}
