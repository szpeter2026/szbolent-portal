<?php
/**
 * Template Name: Bolent 首页
 * 对应外包需求：8 个页面模板之一 — Home
 * 读取 bolent_service CPT 渲染服务卡片
 */
if (!defined('ABSPATH')) exit;
get_header();
?>

<!-- Hero Section -->
<section class="bolent-hero">
    <div class="bolent-container" style="max-width:1200px;margin:0 auto;padding:0 24px;position:relative;z-index:2;">
        <div class="bolent-hero-content" style="padding:60px 0;">
            <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:999px;font-size:13px;color:#fff;margin-bottom:24px;">
                <span style="width:6px;height:6px;background:#E0B85A;border-radius:50%;"></span>
                科技 × 人文 · 数智融合
            </div>
            <h1 style="font-size:52px;font-weight:500;color:#fff;line-height:1.2;margin-bottom:24px;">
                以工程精度交付价值<br>以<span style="color:#E0B85A;font-family:'Noto Serif SC',serif;">人文视角</span>连接未来
            </h1>
            <p style="font-size:19px;color:rgba(255,255,255,0.85);max-width:580px;margin-bottom:40px;line-height:1.7;">
                Bolent 是一家融合现代科技与文化底蕴的数智企业。我们提供全方位 IT 服务，并以 AI 读诗为特色，让技术在诗意中落地。
            </p>
            <div style="display:flex;gap:16px;flex-wrap:wrap;">
                <a href="<?php echo esc_url(home_url('/services')); ?>" style="padding:14px 32px;background:#fff;color:#0E6E6A;border-radius:8px;font-size:15px;font-weight:500;">探索服务 →</a>
                <a href="<?php echo esc_url(home_url('/poetry')); ?>" style="padding:14px 32px;background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,0.5);border-radius:8px;font-size:15px;font-weight:500;">进入诗词</a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="bolent-section">
    <div class="bolent-container" style="max-width:1200px;margin:0 auto;padding:0 24px;">
        <div class="bolent-section-header">
            <span class="bolent-section-eyebrow">OUR SERVICES</span>
            <h2 class="bolent-section-title">全方位数智服务</h2>
            <p class="bolent-section-desc">从软件开发到 AI 诗词，Bolent 以专业团队为您构建数字化解决方案</p>
        </div>
        <div class="bolent-service-grid">
            <?php
            $services = new WP_Query(array(
                'post_type' => 'bolent_service',
                'posts_per_page' => 6,
                'orderby' => 'menu_order',
                'order' => 'ASC',
            ));

            if ($services->have_posts()) :
                while ($services->have_posts()) : $services->the_post();
                    $icon     = get_post_meta(get_the_ID(), '_service_icon', true) ?: '◆';
                    $desc     = get_post_meta(get_the_ID(), '_service_short_desc', true) ?: get_the_excerpt();
                    $featured = get_post_meta(get_the_ID(), '_service_is_featured', true);
                    $features = get_post_meta(get_the_ID(), '_service_features', true);
                    $feat_class = $featured === '1' ? ' featured' : '';
            ?>
                    <div class="bolent-service-card<?php echo esc_attr($feat_class); ?>">
                        <?php if ($featured === '1') : ?>
                            <span style="display:inline-block;padding:3px 10px;background:#FAF3E4;color:#A67D2E;font-size:11px;font-weight:500;border-radius:999px;margin-bottom:12px;">特色板块</span>
                        <?php endif; ?>
                        <div class="bolent-service-icon"><?php echo esc_html($icon); ?></div>
                        <h3 style="font-size:20px;font-weight:500;color:#1A1F1E;margin-bottom:12px;"><?php the_title(); ?></h3>
                        <p style="font-size:15px;color:#5C6663;margin-bottom:20px;line-height:1.7;"><?php echo esc_html($desc); ?></p>
                        <?php if ($features) :
                            $feat_list = explode(',', $features);
                            echo '<ul style="list-style:none;margin-bottom:20px;">';
                            foreach ($feat_list as $feat) {
                                echo '<li style="font-size:14px;color:#5C6663;padding:4px 0 4px 24px;position:relative;">' . esc_html(trim($feat)) . '</li>';
                            }
                            echo '</ul>';
                        endif; ?>
                        <a href="<?php the_permalink(); ?>" style="font-size:14px;color:<?php echo $featured === '1' ? '#A67D2E' : '#0E6E6A'; ?>;font-weight:500;">
                            <?php echo $featured === '1' ? '进入诗词 →' : '了解更多 →'; ?>
                        </a>
                    </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                // 无 CPT 数据时显示默认服务
                $default_services = array(
                    array('icon' => '◆', 'title' => '数字化 & 数据', 'desc' => '实时、准确、可靠的数据采集与分析', 'featured' => false),
                    array('icon' => '▲', 'title' => '软件开发', 'desc' => '从 Web 到移动端，为企业打造数字化产品', 'featured' => false),
                    array('icon' => '●', 'title' => '自动化 & QA', 'desc' => '测试自动化工具与质量保障体系', 'featured' => false),
                    array('icon' => '■', 'title' => 'IT 管理', 'desc' => '企业级 IT 管理与基础设施运维', 'featured' => false),
                    array('icon' => '✦', 'title' => 'AI 读诗', 'desc' => '以人工智能解读古典诗词，科技在诗意中落地', 'featured' => true),
                    array('icon' => '◈', 'title' => 'IT 外包', 'desc' => '不只是成本套利，更是优质人才与高质量交付', 'featured' => false),
                );
                foreach ($default_services as $svc) :
                    $feat_class = $svc['featured'] ? ' featured' : '';
            ?>
                    <div class="bolent-service-card<?php echo esc_attr($feat_class); ?>">
                        <?php if ($svc['featured']) : ?>
                            <span style="display:inline-block;padding:3px 10px;background:#FAF3E4;color:#A67D2E;font-size:11px;font-weight:500;border-radius:999px;margin-bottom:12px;">特色板块</span>
                        <?php endif; ?>
                        <div class="bolent-service-icon"><?php echo esc_html($svc['icon']); ?></div>
                        <h3 style="font-size:20px;font-weight:500;color:#1A1F1E;margin-bottom:12px;"><?php echo esc_html($svc['title']); ?></h3>
                        <p style="font-size:15px;color:#5C6663;margin-bottom:20px;line-height:1.7;"><?php echo esc_html($svc['desc']); ?></p>
                        <a href="<?php echo esc_url(home_url('/services')); ?>" style="font-size:14px;color:<?php echo $svc['featured'] ? '#A67D2E' : '#0E6E6A'; ?>;font-weight:500;">
                            <?php echo $svc['featured'] ? '进入诗词 →' : '了解更多 →'; ?>
                        </a>
                    </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bolent-stats">
    <div class="bolent-container" style="max-width:1200px;margin:0 auto;padding:0 24px;">
        <div class="bolent-stats-grid">
            <div>
                <div class="bolent-stat-value">120<span class="unit">+</span></div>
                <div class="bolent-stat-label">交付项目</div>
            </div>
            <div>
                <div class="bolent-stat-value">98<span class="unit">%</span></div>
                <div class="bolent-stat-label">客户满意度</div>
            </div>
            <div>
                <div class="bolent-stat-value">3000<span class="unit">+</span></div>
                <div class="bolent-stat-label">诗词解析</div>
            </div>
            <div>
                <div class="bolent-stat-value">8<span class="unit">年</span></div>
                <div class="bolent-stat-label">行业经验</div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="bolent-section" style="background:#F7F9F8;">
    <div class="bolent-container" style="max-width:1200px;margin:0 auto;padding:0 24px;">
        <div class="bolent-section-header">
            <span class="bolent-section-eyebrow">WHY BOLENT</span>
            <h2 class="bolent-section-title">为什么选择我们</h2>
            <p class="bolent-section-desc">深耕技术，融通人文，以可信赖的伙伴角色陪伴您的数智化旅程</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:32px;">
            <?php
            $features = array(
                array('icon' => '◆', 'title' => '领域专业', 'desc' => '深厚的行业经验与专业技能积累'),
                array('icon' => '✦', 'title' => '卓越品质', 'desc' => '工程级精度，交付高质量解决方案'),
                array('icon' => '◈', 'title' => '技术前沿', 'desc' => '采用最新技术与行业最佳实践'),
                array('icon' => '❖', 'title' => '人文视角', 'desc' => '以文化底蕴驱动有温度的产品体验'),
            );
            foreach ($features as $feat) :
            ?>
                <div style="text-align:center;padding:40px 24px;background:#fff;border:1px solid #E0E5E3;border-radius:16px;transition:all 0.3s;">
                    <div style="width:64px;height:64px;margin:0 auto 24px;border-radius:50%;background:#E8F4F3;display:flex;align-items:center;justify-content:center;font-size:28px;color:#0E6E6A;"><?php echo esc_html($feat['icon']); ?></div>
                    <h4 style="font-size:18px;font-weight:500;color:#1A1F1E;margin-bottom:12px;"><?php echo esc_html($feat['title']); ?></h4>
                    <p style="font-size:14px;color:#5C6663;line-height:1.6;"><?php echo esc_html($feat['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bolent-cta">
    <div class="bolent-container" style="max-width:640px;margin:0 auto;padding:0 24px;text-align:center;">
        <h2>准备好开启<span style="font-family:'Noto Serif SC',serif;">数智</span>之旅了吗？</h2>
        <p>让我们一起探讨如何以技术助力业务、以人文连接未来</p>
        <a href="<?php echo esc_url(home_url('/contact')); ?>" style="display:inline-flex;padding:14px 32px;background:#fff;color:#0E6E6A;border-radius:8px;font-size:15px;font-weight:500;">立即咨询 →</a>
    </div>
</section>

<?php get_footer(); ?>
