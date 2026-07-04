<?php
/**
 * CPT: Services（服务）
 * 对应外包需求：Custom Post Type 配置 — Services
 * 不依赖 ACF，使用原生 register_post_meta
 */
if (!defined('ABSPATH')) exit;

function bolent_register_cpt_services() {
    $labels = array(
        'name'               => '服务',
        'singular_name'      => '服务',
        'menu_name'          => '服务管理',
        'add_new'            => '新增服务',
        'add_new_item'       => '新增服务',
        'edit_item'          => '编辑服务',
        'new_item'           => '新服务',
        'view_item'          => '查看服务',
        'search_items'       => '搜索服务',
        'not_found'          => '未找到服务',
        'not_found_in_trash' => '回收站为空',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => array('slug' => 'services'),
        'menu_icon'           => 'dashicons-portfolio',
        'menu_position'       => 5,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'        => true, // Gutenberg 支持
    );

    register_post_type('bolent_service', $args);

    // 服务分类法
    register_taxonomy('service_category', 'bolent_service', array(
        'labels' => array(
            'name' => '服务分类',
            'singular_name' => '服务分类',
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'service-category'),
    ));
}
add_action('init', 'bolent_register_cpt_services');

/**
 * 自定义字段（不依赖 ACF，使用 post_meta）
 */
function bolent_register_service_meta() {
    $fields = array(
        'service_icon'        => array('type' => 'string', 'label' => '图标符号'),
        'service_short_desc'  => array('type' => 'string', 'label' => '简短描述'),
        'service_features'    => array('type' => 'string', 'label' => '特点列表(逗号分隔)'),
        'service_slug_ref'    => array('type' => 'string', 'label' => 'URL slug 引用'),
        'service_is_featured' => array('type' => 'boolean', 'label' => '是否特色板块'),
    );

    foreach ($fields as $key => $config) {
        register_post_meta('bolent_service', '_' . $key, array(
            'type'         => $config['type'],
            'single'       => true,
            'show_in_rest' => true,
        ));
    }
}
add_action('init', 'bolent_register_service_meta');

/**
 * 管理后台自定义字段面板（无 ACF 时使用）
 */
function bolent_service_meta_box() {
    add_meta_box('bolent_service_fields', '服务详情', 'bolent_service_meta_box_html', 'bolent_service', 'side', 'default');
}
add_action('add_meta_boxes', 'bolent_service_meta_box');

function bolent_service_meta_box_html($post) {
    wp_nonce_field('bolent_service_meta', 'bolent_service_nonce');
    $icon     = get_post_meta($post->ID, '_service_icon', true);
    $desc     = get_post_meta($post->ID, '_service_short_desc', true);
    $features = get_post_meta($post->ID, '_service_features', true);
    $featured = get_post_meta($post->ID, '_service_is_featured', true);
    ?>
    <p><label>图标符号<br><input type="text" name="service_icon" value="<?php echo esc_attr($icon); ?>" placeholder="◆" style="width:100%"></label></p>
    <p><label>简短描述<br><input type="text" name="service_short_desc" value="<?php echo esc_attr($desc); ?>" style="width:100%"></label></p>
    <p><label>特点列表(逗号分隔)<br><input type="text" name="service_features" value="<?php echo esc_attr($features); ?>" placeholder="数据采集,实时分析,BI可视化" style="width:100%"></label></p>
    <p><label><input type="checkbox" name="service_is_featured" value="1" <?php checked($featured, '1'); ?>> 特色板块（琥珀金风格）</label></p>
    <?php
}

function bolent_save_service_meta($post_id) {
    if (!isset($_POST['bolent_service_nonce']) || !wp_verify_nonce($_POST['bolent_service_nonce'], 'bolent_service_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array('service_icon', 'service_short_desc', 'service_features');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    update_post_meta($post_id, '_service_is_featured', isset($_POST['service_is_featured']) ? '1' : '0');
}
add_action('save_post_bolent_service', 'bolent_save_service_meta');
