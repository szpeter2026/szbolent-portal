<?php
/**
 * Bolent Astra Child Theme — functions.php
 * 项目：szbolent-portal
 * 功能：样式加载、CPT 注册、导航菜单、CF7 支持
 */

if (!defined('ABSPATH')) exit;

define('BOLENT_VERSION', '1.0.0');
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

    // Google Fonts: Manrope + Noto Serif SC + Noto Sans SC
    wp_enqueue_style('bolent-fonts', 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&family=Noto+Serif+SC:wght@400;500;700&family=Noto+Sans+SC:wght@400;500;700&display=swap', array(), null);

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
 * 3. 加载 Custom Post Type 配置
 */
require_once BOLENT_DIR . '/cpt/services.php';
require_once BOLENT_DIR . '/cpt/careers.php';
require_once BOLENT_DIR . '/cpt/case-studies.php';

/**
 * 4. Astra Customizer 覆盖
 * 将 Astra 默认配色替换为 Bolent 品牌色
 */
function bolent_astra_customizer_defaults($wp_customize) {
    // 仅在 Astra 主题激活时生效
    if (function_exists('astra_get_option')) {
        // Primary Color
        $wp_customize->get_setting('astra-settings[link-color]')->default = '#0E6E6A';
        // Text Color
        $wp_customize->get_setting('astra-settings[text-color]')->default = '#2C3331';
        // Heading Color
        $wp_customize->get_setting('astra-settings[heading-color]')->default = '#1A1F1E';
    }
}
add_action('customize_register', 'bolent_astra_customizer_defaults', 20);

/**
 * 5. 页面模板注册
 */
function bolent_page_templates($templates) {
    $templates['page-templates/page-home.php'] = __('Bolent 首页', 'bolent-astra-child');
    $templates['page-templates/page-services.php'] = __('Bolent 服务列表', 'bolent-astra-child');
    $templates['page-templates/page-contact.php'] = __('Bolent 联系我们', 'bolent-astra-child');
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
 * 8. 自定义_excerpt 长度
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
 * 9. 预设示例内容激活钩子（Phase 4 使用）
 */
function bolent_import_demo_content() {
    if (get_option('bolent_demo_imported') !== 'yes') {
        require_once BOLENT_DIR . '/acf-fields/demo-content.php';
        update_option('bolent_demo_imported', 'yes');
    }
}
add_action('after_switch_theme', 'bolent_import_demo_content');
