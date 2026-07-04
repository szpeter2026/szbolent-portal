<?php
/**
 * Bolent 自定义 Header 模板
 * 对应外包需求：Header 模板 1 个（含移动端汉堡菜单）
 */
if (!defined('ABSPATH')) exit;
?>
<header class="bolent-header">
    <div class="bolent-header-inner">
        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="bolent-logo">
            <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <rect x="2" y="4" width="14" height="14" rx="3" fill="#0E6E6A"/>
                <rect x="2" y="20" width="14" height="8" rx="3" fill="#0E6E6A" opacity="0.55"/>
                <path d="M18 4 Q30 12 18 30" stroke="#0E6E6A" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <circle cx="26" cy="12" r="3.5" fill="#C99A3F"/>
            </svg>
            <span class="bolent-logo-text">Bolent</span>
        </a>

        <!-- 导航菜单 -->
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => 'nav',
            'container_class' => 'bolent-nav',
            'menu_class' => '',
            'fallback_cb' => 'bolent_default_menu',
        ));
        ?>

        <!-- CTA 按钮 -->
        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="bolent-header-cta">立即咨询</a>

        <!-- 移动端汉堡菜单 -->
        <div class="bolent-mobile-toggle" onclick="bolentToggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </div>
</header>

<?php
/**
 * 默认菜单（未设置菜单时显示）
 */
function bolent_default_menu() {
    $items = array(
        '/' => '首页',
        '/about' => '关于我们',
        '/services' => '服务',
        '/poetry' => '诗词',
        '/case-study' => '案例',
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
?>

<script>
function bolentToggleMenu() {
    var nav = document.querySelector('.bolent-nav');
    nav.classList.toggle('bolent-nav-open');
}
</script>
