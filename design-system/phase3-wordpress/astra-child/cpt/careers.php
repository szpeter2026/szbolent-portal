<?php
/**
 * CPT: Careers（招贤纳士 / 职位）
 * 对应外包需求：Custom Post Type 配置 — Careers (Jobs)
 */
if (!defined('ABSPATH')) exit;

function bolent_register_cpt_careers() {
    $labels = array(
        'name'               => '职位',
        'singular_name'      => '职位',
        'menu_name'          => '招贤纳士',
        'add_new'            => '发布职位',
        'add_new_item'       => '发布新职位',
        'edit_item'          => '编辑职位',
        'view_item'          => '查看职位',
        'search_items'       => '搜索职位',
        'not_found'          => '暂无职位',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'careers'),
        'menu_icon'     => 'dashicons-businessperson',
        'menu_position' => 6,
        'supports'      => array('title', 'editor', 'custom-fields'),
        'show_in_rest'  => true,
    );

    register_post_type('bolent_job', $args);

    // 职位分类法：部门
    register_taxonomy('job_department', 'bolent_job', array(
        'labels'       => array('name' => '部门', 'singular_name' => '部门'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));

    // 职位分类法：地点
    register_taxonomy('job_location', 'bolent_job', array(
        'labels'       => array('name' => '工作地点', 'singular_name' => '地点'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));

    // 职位分类法：类型
    register_taxonomy('job_type', 'bolent_job', array(
        'labels'       => array('name' => '工作类型', 'singular_name' => '类型'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
}
add_action('init', 'bolent_register_cpt_careers');

/**
 * 职位自定义字段
 */
function bolent_register_job_meta() {
    $fields = array(
        'job_salary_min'   => array('type' => 'integer', 'label' => '薪资下限'),
        'job_salary_max'   => array('type' => 'integer', 'label' => '薪资上限'),
        'job_experience'   => array('type' => 'string',  'label' => '经验要求'),
        'job_education'    => array('type' => 'string',  'label' => '学历要求'),
        'job_requirements' => array('type' => 'string',  'label' => '职位要求(换行分隔)'),
        'job_status'       => array('type' => 'string',  'label' => '状态(open/closed)'),
    );

    foreach ($fields as $key => $config) {
        register_post_meta('bolent_job', '_' . $key, array(
            'type'         => $config['type'],
            'single'       => true,
            'show_in_rest' => true,
        ));
    }
}
add_action('init', 'bolent_register_job_meta');

/**
 * 管理后台字段面板
 */
function bolent_job_meta_box() {
    add_meta_box('bolent_job_fields', '职位详情', 'bolent_job_meta_box_html', 'bolent_job', 'normal', 'default');
}
add_action('add_meta_boxes', 'bolent_job_meta_box');

function bolent_job_meta_box_html($post) {
    wp_nonce_field('bolent_job_meta', 'bolent_job_nonce');
    $salary_min = get_post_meta($post->ID, '_job_salary_min', true);
    $salary_max = get_post_meta($post->ID, '_job_salary_max', true);
    $exp        = get_post_meta($post->ID, '_job_experience', true);
    $edu        = get_post_meta($post->ID, '_job_education', true);
    $reqs       = get_post_meta($post->ID, '_job_requirements', true);
    $status     = get_post_meta($post->ID, '_job_status', true) ?: 'open';
    ?>
    <table style="width:100%">
        <tr><td><label>薪资范围 (K)</label></td><td><input type="number" name="job_salary_min" value="<?php echo esc_attr($salary_min); ?>" placeholder="15"> — <input type="number" name="job_salary_max" value="<?php echo esc_attr($salary_max); ?>" placeholder="30"></td></tr>
        <tr><td><label>经验要求</label></td><td><input type="text" name="job_experience" value="<?php echo esc_attr($exp); ?>" placeholder="3-5年"></td></tr>
        <tr><td><label>学历要求</label></td><td><input type="text" name="job_education" value="<?php echo esc_attr($edu); ?>" placeholder="本科及以上"></td></tr>
        <tr><td><label>职位要求</label></td><td><textarea name="job_requirements" rows="5" style="width:100%" placeholder="每行一条要求"><?php echo esc_textarea($reqs); ?></textarea></td></tr>
        <tr><td><label>状态</label></td><td><select name="job_status"><option value="open" <?php selected($status, 'open'); ?>>招聘中</option><option value="closed" <?php selected($status, 'closed'); ?>>已关闭</option></select></td></tr>
    </table>
    <?php
}

function bolent_save_job_meta($post_id) {
    if (!isset($_POST['bolent_job_nonce']) || !wp_verify_nonce($_POST['bolent_job_nonce'], 'bolent_job_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['job_salary_min']))   update_post_meta($post_id, '_job_salary_min', intval($_POST['job_salary_min']));
    if (isset($_POST['job_salary_max']))   update_post_meta($post_id, '_job_salary_max', intval($_POST['job_salary_max']));
    if (isset($_POST['job_experience']))   update_post_meta($post_id, '_job_experience', sanitize_text_field($_POST['job_experience']));
    if (isset($_POST['job_education']))    update_post_meta($post_id, '_job_education', sanitize_text_field($_POST['job_education']));
    if (isset($_POST['job_requirements'])) update_post_meta($post_id, '_job_requirements', sanitize_textarea_field($_POST['job_requirements']));
    if (isset($_POST['job_status']))       update_post_meta($post_id, '_job_status', sanitize_text_field($_POST['job_status']));
}
add_action('save_post_bolent_job', 'bolent_save_job_meta');
