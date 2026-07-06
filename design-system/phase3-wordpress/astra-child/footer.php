<?php
/**
 * Bolent 自定义 Footer 模板
 * 对应外包需求：Footer 模板 1 个
 */
if (!defined('ABSPATH')) exit;
?>
<footer class="bolent-footer">
    <div class="bolent-footer-grid">
        <!-- 品牌区 -->
        <div class="bolent-footer-brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="bolent-logo">
                <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                    <rect x="2" y="4" width="14" height="14" rx="3" fill="#FFFFFF"/>
                    <rect x="2" y="20" width="14" height="8" rx="3" fill="#FFFFFF" opacity="0.6"/>
                    <path d="M18 4 Q30 12 18 30" stroke="#FFFFFF" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    <circle cx="26" cy="12" r="3.5" fill="#E0B85A"/>
                </svg>
                <span class="bolent-logo-text" style="color:#fff">Bolent</span>
            </a>
            <p>融合现代科技与文化底蕴的数智企业。以工程精度交付价值，以人文视角连接未来。</p>
        </div>

        <!-- 服务 -->
        <div class="bolent-footer-col">
            <h5>服务</h5>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer-services',
                'container' => false,
                'menu_class' => '',
                'fallback_cb' => function() {
                    echo '<ul>';
                    $services = array('软件开发'=>'/services/development', '数字化 & 数据'=>'/services/digital', '自动化 & QA'=>'/services/automation', 'IT 外包'=>'/services/outsourcing');
                    foreach ($services as $label => $slug) {
                        echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($label) . '</a></li>';
                    }
                    echo '</ul>';
                },
            ));
            ?>
        </div>

        <!-- 探索 -->
        <div class="bolent-footer-col">
            <h5>探索</h5>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer-explore',
                'container' => false,
                'menu_class' => '',
                'fallback_cb' => function() {
                    echo '<ul>';
                    $explore = array('AI 读诗'=>'/poetry', '成功案例'=>'/case-study', '技术博客'=>'/blog', '关于我们'=>'/about');
                    foreach ($explore as $label => $slug) {
                        echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($label) . '</a></li>';
                    }
                    echo '</ul>';
                },
            ));
            ?>
        </div>

        <!-- 联系 -->
        <div class="bolent-footer-col">
            <h5>联系</h5>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer-contact',
                'container' => false,
                'menu_class' => '',
                'fallback_cb' => function() {
                    echo '<ul>';
                    $contact = array('联系我们'=>'/contact', '招贤纳士'=>'/careers', '合作伙伴'=>'/partners');
                    foreach ($contact as $label => $slug) {
                        echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($label) . '</a></li>';
                    }
                    echo '</ul>';
                },
            ));
            ?>
        </div>
    </div>
    <div class="bolent-footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> Bolent. All rights reserved.</span>
        <span>www.szbolent.com.cn</span>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
