<template>
  <div class="case-study-page">
    <!-- Hero Section -->
    <section class="case-hero">
      <div class="container">
        <h1 class="hero-title" data-aos="fade-up">成功案例</h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
          见证我们如何助力企业实现数字化转型，创造卓越价值
        </p>
      </div>
    </section>

    <!-- 筛选器 -->
    <section class="filter-section">
      <div class="container">
        <div class="filter-bar" data-aos="fade-up">
          <button 
            v-for="industry in industries" 
            :key="industry.id"
            :class="['filter-btn', { active: selectedIndustry === industry.id }]"
            @click="selectedIndustry = industry.id"
          >
            {{ industry.name }}
          </button>
        </div>
      </div>
    </section>

    <!-- 案例网格 -->
    <section class="cases-section section">
      <div class="container">
        <div class="cases-grid">
          <div 
            v-for="(caseItem, index) in filteredCases" 
            :key="caseItem.id"
            class="case-card"
            data-aos="fade-up"
            :data-aos-delay="index % 3 * 100"
          >
            <div class="case-image">
              <img :src="caseItem.image" :alt="caseItem.title" />
              <div class="case-overlay">
                <router-link :to="`/case-study/${caseItem.slug}`" class="view-btn">
                  查看详情 →
                </router-link>
              </div>
            </div>
            <div class="case-content">
              <div class="case-meta">
                <span class="industry-tag">{{ caseItem.industry }}</span>
                <span class="service-tag">{{ caseItem.service }}</span>
              </div>
              <h3 class="case-title">{{ caseItem.title }}</h3>
              <p class="case-description">{{ caseItem.description }}</p>
              <div class="case-stats">
                <div class="stat-item" v-for="stat in caseItem.stats" :key="stat.label">
                  <div class="stat-value">{{ stat.value }}</div>
                  <div class="stat-label">{{ stat.label }}</div>
                </div>
              </div>
              <router-link :to="`/case-study/${caseItem.slug}`" class="read-more">
                阅读完整案例 →
              </router-link>
            </div>
          </div>
        </div>

        <!-- 空状态 -->
        <div v-if="filteredCases.length === 0" class="empty-state">
          <div class="empty-icon">📋</div>
          <p>该行业暂无案例</p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content" data-aos="zoom-in">
          <h3>想要成为下一个成功案例？</h3>
          <p>让我们一起探讨如何助力您的业务腾飞</p>
          <router-link to="/contact" class="btn btn-primary btn-large">
            开始您的项目
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AOS from 'aos'

// 行业分类
const industries = [
  { id: 'all', name: '全部' },
  { id: 'fintech', name: '金融科技' },
  { id: 'ecommerce', name: '电商零售' },
  { id: 'healthcare', name: '医疗健康' },
  { id: 'education', name: '在线教育' },
  { id: 'enterprise', name: '企业服务' },
  { id: 'logistics', name: '物流配送' }
]

const selectedIndustry = ref('all')

// 案例数据
const cases = ref([
  {
    id: 1,
    slug: 'fintech-payment-platform',
    title: '某支付平台核心系统重构',
    description: '为国内领先的第三方支付平台重构核心交易系统，实现高并发处理能力和系统稳定性的大幅提升。',
    industry: '金融科技',
    industryId: 'fintech',
    service: 'IT 外包',
    image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop',
    stats: [
      { label: '并发处理提升', value: '300%' },
      { label: '系统稳定性', value: '99.99%' },
      { label: '响应时间降低', value: '60%' }
    ]
  },
  {
    id: 2,
    slug: 'ecommerce-omnichannel',
    title: '全渠道电商平台建设',
    description: '为某知名零售品牌打造全渠道电商平台，整合线上线下资源，实现会员数据统一管理。',
    industry: '电商零售',
    industryId: 'ecommerce',
    service: '定制开发',
    image: 'https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?w=800&h=600&fit=crop',
    stats: [
      { label: '用户增长', value: '150%' },
      { label: '转化率提升', value: '80%' },
      { label: '客单价增长', value: '45%' }
    ]
  },
  {
    id: 3,
    slug: 'healthcare-appointment-system',
    title: '智能预约挂号系统',
    description: '为三甲医院开发智能预约挂号系统，优化就医流程，提升患者体验和医院运营效率。',
    industry: '医疗健康',
    industryId: 'healthcare',
    service: '敏捷咨询',
    image: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=600&fit=crop',
    stats: [
      { label: '预约效率提升', value: '200%' },
      { label: '患者满意度', value: '95%' },
      { label: '运营成本降低', value: '40%' }
    ]
  },
  {
    id: 4,
    slug: 'education-learning-platform',
    title: '在线学习平台开发',
    description: '为教育机构打造AI驱动的在线学习平台，实现个性化学习路径推荐和智能作业批改。',
    industry: '在线教育',
    industryId: 'education',
    service: '定制开发',
    image: 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=800&h=600&fit=crop',
    stats: [
      { label: '学员增长', value: '250%' },
      { label: '完课率提升', value: '70%' },
      { label: '教学效率', value: '+120%' }
    ]
  },
  {
    id: 5,
    slug: 'enterprise-crm-system',
    title: '企业 CRM 系统升级',
    description: '为大型企业升级 CRM 系统，整合销售、市场、客服数据，实现客户全生命周期管理。',
    industry: '企业服务',
    industryId: 'enterprise',
    service: '数字化转型',
    image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
    stats: [
      { label: '销售效率', value: '+180%' },
      { label: '客户留存率', value: '92%' },
      { label: 'ROI 提升', value: '320%' }
    ]
  },
  {
    id: 6,
    slug: 'logistics-tracking-system',
    title: '物流追踪管理系统',
    description: '为物流公司开发实时追踪系统，实现货物全程可视化管理和智能调度优化。',
    industry: '物流配送',
    industryId: 'logistics',
    service: 'IT 外包',
    image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop',
    stats: [
      { label: '配送效率', value: '+160%' },
      { label: '准时率', value: '98%' },
      { label: '成本节约', value: '35%' }
    ]
  }
])

// 筛选案例
const filteredCases = computed(() => {
  if (selectedIndustry.value === 'all') {
    return cases.value
  }
  return cases.value.filter(c => c.industryId === selectedIndustry.value)
})

onMounted(() => {
  AOS.refresh()
})
</script>

<style scoped lang="scss">
.case-study-page {
  .case-hero {
    background: var(--bolent-gradient);
    color: white;
    padding: 100px 0 80px;
    text-align: center;

    .hero-title {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .hero-subtitle {
      font-size: 20px;
      opacity: 0.95;
      max-width: 600px;
      margin: 0 auto;
    }
  }

  .filter-section {
    padding: 40px 0;
    background: var(--bolent-bg-soft);

    .filter-bar {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .filter-btn {
      padding: 10px 24px;
      border: 2px solid var(--bolent-border);
      background: white;
      border-radius: 25px;
      font-size: 15px;
      cursor: pointer;
      transition: var(--bolent-transition);
      color: var(--bolent-text);
      font-weight: 500;

      &:hover {
        border-color: var(--bolent-primary);
        color: var(--bolent-primary);
        transform: translateY(-2px);
      }

      &.active {
        background: var(--bolent-primary);
        border-color: var(--bolent-primary);
        color: white;
      }
    }
  }

  .cases-section {
    .cases-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 40px;
      margin-bottom: 40px;
    }

    .case-card {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: var(--bolent-shadow);
      transition: var(--bolent-transition);

      &:hover {
        transform: translateY(-8px);
        box-shadow: var(--bolent-shadow-lg);

        .case-overlay {
          opacity: 1;
        }
      }

      .case-image {
        position: relative;
        height: 240px;
        overflow: hidden;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.5s ease;
        }

        .case-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(14, 110, 106, 0.9);
          display: flex;
          align-items: center;
          justify-content: center;
          opacity: 0;
          transition: opacity 0.3s ease;

          .view-btn {
            padding: 12px 32px;
            background: white;
            color: var(--bolent-primary);
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--bolent-transition);

            &:hover {
              transform: scale(1.05);
              box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
            }
          }
        }
      }

      &:hover .case-image img {
        transform: scale(1.1);
      }

      .case-content {
        padding: 30px;

        .case-meta {
          display: flex;
          gap: 10px;
          margin-bottom: 16px;

          .industry-tag,
          .service-tag {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
          }

          .industry-tag {
            background: var(--bolent-primary-50);
            color: var(--bolent-primary);
          }

          .service-tag {
            background: #f6ffed;
            color: #52c41a;
          }
        }

        .case-title {
          font-size: 22px;
          font-weight: 700;
          margin-bottom: 12px;
          color: var(--bolent-ink);
          line-height: 1.4;
        }

        .case-description {
          color: var(--bolent-text-secondary);
          line-height: 1.6;
          margin-bottom: 20px;
          font-size: 15px;
        }

        .case-stats {
          display: grid;
          grid-template-columns: repeat(3, 1fr);
          gap: 15px;
          margin-bottom: 20px;
          padding: 20px 0;
          border-top: 1px solid #f0f0f0;
          border-bottom: 1px solid #f0f0f0;

          .stat-item {
            text-align: center;

            .stat-value {
              font-size: 24px;
              font-weight: 700;
              color: var(--bolent-primary);
              margin-bottom: 4px;
            }

            .stat-label {
              font-size: 12px;
              color: var(--bolent-text-muted);
            }
          }
        }

        .read-more {
          display: inline-block;
          color: var(--bolent-primary);
          font-weight: 600;
          text-decoration: none;
          transition: var(--bolent-transition);

          &:hover {
            color: var(--bolent-primary-dark);
            transform: translateX(4px);
          }
        }
      }
    }

    .empty-state {
      text-align: center;
      padding: 80px 20px;
      color: var(--bolent-text-muted);

      .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
      }

      p {
        font-size: 18px;
      }
    }
  }

  .cta-section {
    background: var(--bolent-gradient);
    color: white;
    padding: 80px 0;

    .cta-content {
      text-align: center;

      h3 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 16px;
      }

      p {
        font-size: 18px;
        margin-bottom: 32px;
        opacity: 0.95;
      }

      .btn-large {
        padding: 16px 48px;
        font-size: 18px;
        background: white;
        color: var(--bolent-primary);
        border: none;

        &:hover {
          background: var(--bolent-border-light);
          transform: translateY(-3px);
        }
      }
    }
  }
}

/* 响应式 */
@media (max-width: 768px) {
  .case-study-page {
    .case-hero {
      padding: 60px 0 40px;

      .hero-title {
        font-size: 32px;
      }

      .hero-subtitle {
        font-size: 16px;
      }
    }

    .filter-section {
      padding: 20px 0;

      .filter-btn {
        padding: 8px 16px;
        font-size: 14px;
      }
    }

    .cases-section {
      .cases-grid {
        grid-template-columns: 1fr;
        gap: 24px;
      }

      .case-card {
        .case-content {
          padding: 20px;

          .case-title {
            font-size: 18px;
          }

          .case-stats {
            gap: 10px;

            .stat-value {
              font-size: 20px;
            }
          }
        }
      }
    }

    .cta-section {
      padding: 50px 0;

      .cta-content {
        h3 {
          font-size: 24px;
        }

        p {
          font-size: 16px;
        }
      }
    }
  }
}
</style>
