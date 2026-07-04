<?php
/**
 * CPT: Case Studies（成功案例）
 * 对应外包需求：Custom Post Type 配置 — CaseStudies
 */
if (!defined('ABSPATH')) exit;

function bolent_register_cpt_case_studies() {
    $labels = array(
        'name'               => '成功案例',
        'singular_name'      => '案例',
        'menu_name'          => '成功案例',
        'add_new'            => '新增案例',
        'add_new_item'       => '新增案例',
        'edit_item'          => '编辑案例',
        'view_item'          => '查看案例',
        'search_items'       => '搜索案例',
        'not_found'          => '暂无案例',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'case-study'),
        'menu_icon'     => 'dashicons-awards',
        'menu_position' => 7,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'  => true,
    );

    register_post_type('bolent_case', $args);

    // 行业分类法
    register_taxonomy('case_industry', 'bolent_case', array(
        'labels'       => array('name' => '行业', 'singular_name' => '行业'),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'case-industry'),
    ));
}
add_action('init', 'bolent_register_cpt_case_studies');

/**
 * 案例自定义字段
 */
function bolent_register_case_meta() {
    $fields = array(
        'case_client'       => array('type' => 'string', 'label' => '客户名称'),
        'case_tags'         => array('type' => 'string', 'label' => '标签(逗号分隔)'),
        'case_metric_1'     => array('type' => 'string', 'label' => '数据指标1'),
        'case_metric_2'     => array('type' => 'string', 'label' => '数据指标2'),
        'case_metric_3'     => array('type' => 'string', 'label' => '数据指标3'),
        'case_result'       => array('type' => 'string', 'label' => '成果摘要'),
    );

    foreach ($fields as $key => $config) {
        register_post_meta('bolent_case', '_' . $key, array(
            'type'         => $config['type'],
            'single'       => true,
            'show_in_rest' => true,
        ));
    }
}
add_action('init', 'bolent_register_case_meta');

/**
 * 管理后台字段面板
 */
function bolent_case_meta_box() {
    add_meta_box('bolent_case_fields', '案例详情', 'bolent_case_meta_box_html', 'bolent_case', 'normal', 'default');
}
add_action('add_meta_boxes', 'bolent_case_meta_box');

function bolent_case_meta_box_html($post) {
    wp_nonce_field('bolent_case_meta', 'bolent_case_nonce');
    $client  = get_post_meta($post->ID, '_case_client', true);
    $tags    = get_post_meta($post->ID, '_case_tags', true);
    $m1      = get_post_meta($post->ID, '_case_metric_1', true);
    $m2      = get_post_meta($post->ID, '_case_metric_2', true);
    $m3      = get_post_meta($post->ID, '_case_metric_3', true);
    $result  = get_post_meta($post->ID, '_case_result', true);
    ?>
    <table style="width:100%">
        <tr><td style="width:120px"><label>客户名称</label></td><td><input type="text" name="case_client" value="<?php echo esc_attr($client); ?>" style="width:100%"></td></tr>
        <tr><td><label>标签</label></td><td><input type="text" name="case_tags" value="<?php echo esc_attr($tags); ?>" placeholder="Web开发,数据可视化" style="width:100%"></td></tr>
        <tr><td><label>数据指标1</label></td><td><input type="text" name="case_metric_1" value="<?php echo esc_attr($m1); ?>" placeholder="效率提升 40%" style="width:100%"></td></tr>
        <tr><td><label>数据指标2</label></td><td><input type="text" name="case_metric_2" value="<?php echo esc_attr($m2); ?>" placeholder="交付周期缩短 30%" style="width:100%"></td></tr>
        <tr><td><label>数据指标3</label></td><td><input type="text" name="case_metric_3" value="<?php echo esc_attr($m3); ?>" placeholder="用户增长 200%" style="width:100%"></td></tr>
        <tr><td><label>成果摘要</label></td><td><textarea name="case_result" rows="3" style="width:100%"><?php echo esc_textarea($result); ?></textarea></td></tr>
    </table>
    <?php
}

function bolent_save_case_meta($post_id) {
    if (!isset($_POST['bolent_case_nonce']) || !wp_verify_nonce($_POST['bolent_case_nonce'], 'bolent_case_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array('case_client', 'case_tags', 'case_metric_1', 'case_metric_2', 'case_metric_3');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    if (isset($_POST['case_result'])) {
        update_post_meta($post_id, '_case_result', sanitize_textarea_field($_POST['case_result']));
    }
}
add_action('save_post_bolent_case', 'bolent_save_case_meta');
