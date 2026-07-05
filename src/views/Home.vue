<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="container">
        <div class="hero-content" data-aos="fade-up">
          <p class="hero-subtitle">{{ company.name }}</p>
          <h1 class="hero-title" v-html="heroTitle"></h1>
          <h2 class="hero-description">{{ company.tagline }}</h2>
          <div class="hero-actions">
            <router-link to="/poetry" class="btn btn-primary btn-large">进入诗词</router-link>
            <router-link to="/blog" class="btn btn-outline btn-large">阅读博客</router-link>
          </div>
        </div>
      </div>
      
      <!-- 社交媒体链接 -->
      <div class="social-sidebar">
        <a href="#" target="_blank" class="social-link">
          <i class="icon-twitter">𝕏</i>
        </a>
        <a href="#" target="_blank" class="social-link">
          <i class="icon-facebook">f</i>
        </a>
        <a href="#" target="_blank" class="social-link">
          <i class="icon-instagram">📷</i>
        </a>
        <a href="#" target="_blank" class="social-link">
          <i class="icon-linkedin">in</i>
        </a>
      </div>
    </section>

    <!-- 服务展示 Section -->
    <section class="services-section section">
      <div class="container">
        <div class="section-header">
          <div class="section-header-left" data-aos="fade-right">
            <h3 class="section-title">我们的服务</h3>
          </div>
          <div class="section-header-right" data-aos="fade-left">
            <p class="section-description">
              Bolent 提供全方位的 IT 服务解决方案。我们拥有经验丰富且训练有素的专业团队，
              为您提供最优质的软件相关服务。
            </p>
          </div>
        </div>

        <div class="services-grid">
          <div 
            v-for="(service, index) in services" 
            :key="service.slug"
            class="service-card"
            data-aos="fade-up"
            :data-aos-delay="index * 100"
          >
            <div class="service-icon">
              <span>{{ service.icon }}</span>
            </div>
            <h4 class="service-title">{{ service.title }}</h4>
            <p class="service-description">{{ service.description }}</p>
            <router-link :to="`/services/${service.slug}`" class="service-link">
              了解更多 →
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- 为什么选择我们 -->
    <section class="why-choose-us section" style="background: var(--bg-light);">
      <div class="container">
        <div class="section-title" data-aos="fade-up">
          <h3>为什么选择我们</h3>
          <p>我们致力于为客户提供卓越的服务和创新的解决方案</p>
        </div>

        <div class="features-grid">
          <div 
            v-for="(feature, index) in features" 
            :key="index"
            class="feature-card"
            data-aos="zoom-in"
            :data-aos-delay="index * 100"
          >
            <div class="feature-icon">{{ feature.icon }}</div>
            <h4>{{ feature.title }}</h4>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content" data-aos="fade-up">
          <h3>准备好开始您的项目了吗？</h3>
          <p>让我们一起探讨如何助力您的业务成功</p>
          <router-link to="/contact" class="btn btn-primary btn-large">立即咨询</router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { companyInfo as company } from '@/config/company'

const heroTitle = computed(() => 'szbolent.cn<br>智能化诗词门户')

const services = ref([
  {
    icon: '💾',
    title: '数字化 & 数据',
    description: '数字数据收集和基于结果的分析是实时、准确和可靠的。',
    slug: 'digital'
  },
  {
    icon: '💻',
    title: '软件开发',
    description: '应用开发构成了每个企业的基础。它有助于为每个企业数字化创建身份。',
    slug: 'development'
  },
  {
    icon: '🤖',
    title: '自动化 & QA',
    description: '测试自动化工具和质量保证专家帮助企业更快地测试、审查和获取数据。',
    slug: 'automation'
  },
  {
    icon: '⚙️',
    title: 'IT 管理',
    description: '我们的 IT 管理软件解决方案帮助企业监控和控制所有计算机网络。',
    slug: 'it-management'
  },
  {
    icon: '🚀',
    title: '敏捷咨询',
    description: '我们的敏捷方法是帮助企业将人员与所需的工作方式相匹配。',
    slug: 'agile'
  },
  {
    icon: '🌐',
    title: 'IT 外包',
    description: 'Bolent 的外包服务不仅仅是成本套利，更是为客户提供最优秀的人才和高质量的工作。',
    slug: 'outsourcing'
  }
])

const features = ref([
  {
    icon: '🎯',
    title: '领域专业知识',
    description: '深厚的行业经验和专业技能'
  },
  {
    icon: '✨',
    title: '卓越品质',
    description: '致力于提供高质量的解决方案'
  },
  {
    icon: '🔧',
    title: '技术卓越',
    description: '采用最新技术和最佳实践'
  },
  {
    icon: '🤝',
    title: '客户至上',
    description: '以客户需求为核心驱动力'
  }
])
</script>

<style scoped lang="scss">
.home-page {
  .hero-section {
    position: relative;
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bolent-gradient);
    overflow: hidden;

    // 动态背景图案
    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 40%),
        radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 40%),
        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
      background-size: cover;
      animation: backgroundShift 20s ease-in-out infinite;
    }

    // 浮动圆圈装饰
    &::after {
      content: '';
      position: absolute;
      width: 300px;
      height: 300px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      top: 10%;
      right: 15%;
      animation: float 8s ease-in-out infinite;
    }

    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
    }

    .container {
      position: relative;
      z-index: 1;
    }

    .hero-content {
      text-align: center;
      color: white;
      max-width: 800px;
      margin: 0 auto;
      padding: 40px 20px;

      .hero-subtitle {
        font-size: 20px;
        font-weight: 500;
        margin-bottom: 16px;
        opacity: 0.9;
        letter-spacing: 2px;
        text-transform: uppercase;
      }

      .hero-title {
        font-size: 4rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 24px;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      }

      .hero-description {
        font-size: 1.5rem;
        font-weight: 400;
        margin-bottom: 40px;
        opacity: 0.95;
      }

      .hero-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;

        .btn {
          min-width: 160px;
        }

        .btn-outline {
          background: transparent;
          border: 2px solid white;
          color: white;

          &:hover {
            background: white;
            color: var(--primary-color);
          }
        }
      }
    }

    .social-sidebar {
      position: absolute;
      right: 40px;
      top: 50%;
      transform: translateY(-50%);
      display: flex;
      flex-direction: column;
      gap: 16px;
      z-index: 2;

      .social-link {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        transition: var(--bolent-transition);

        &:hover {
          background: white;
          color: var(--primary-color);
          transform: scale(1.1);
        }
      }
    }
  }

  .services-section {
    .section-header {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 60px;
      margin-bottom: 60px;
      align-items: start;

      .section-title {
        font-size: 2.5rem;
        color: var(--text-dark);
        margin: 0;
      }

      .section-description {
        font-size: 1.125rem;
        line-height: 1.8;
        color: var(--text-light);
        margin: 0;
      }
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 32px;

      .service-card {
        background: white;
        border-radius: 12px;
        padding: 40px 32px;
        box-shadow: var(--bolent-shadow);
        transition: var(--bolent-transition);
        cursor: pointer;

        &:hover {
          transform: translateY(-8px);
          box-shadow: var(--bolent-shadow-lg);

          .service-icon span {
            transform: scale(1.1);
          }
        }

        .service-icon {
          width: 80px;
          height: 80px;
          border-radius: 16px;
          background: var(--bolent-gradient);
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 24px;

          span {
            font-size: 40px;
            transition: transform 0.3s;
          }
        }

        .service-title {
          font-size: 1.5rem;
          margin-bottom: 16px;
          color: var(--text-dark);
        }

        .service-description {
          color: var(--text-light);
          line-height: 1.6;
          margin-bottom: 24px;
        }

        .service-link {
          color: var(--primary-color);
          font-weight: 600;
          font-size: 15px;
          display: inline-flex;
          align-items: center;
          gap: 8px;
          transition: gap 0.3s;

          &:hover {
            gap: 12px;
          }
        }
      }
    }
  }

  .why-choose-us {
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 32px;
      margin-top: 60px;

      .feature-card {
        text-align: center;
        padding: 40px 24px;
        background: white;
        border-radius: 12px;
        transition: var(--bolent-transition);

        &:hover {
          transform: translateY(-8px);
          box-shadow: var(--bolent-shadow-lg);
        }

        .feature-icon {
          font-size: 60px;
          margin-bottom: 24px;
        }

        h4 {
          font-size: 1.25rem;
          margin-bottom: 16px;
          color: var(--text-dark);
        }

        p {
          color: var(--text-light);
          line-height: 1.6;
        }
      }
    }
  }

  .cta-section {
    background: var(--bolent-gradient);
    padding: 100px 0;

    .cta-content {
      text-align: center;
      color: white;

      h3 {
        font-size: 2.5rem;
        margin-bottom: 16px;
      }

      p {
        font-size: 1.25rem;
        margin-bottom: 40px;
        opacity: 0.9;
      }

      .btn {
        background: white;
        color: var(--primary-color);

        &:hover {
          transform: translateY(-4px);
          box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }
      }
    }
  }
}

/* 动画效果 */
@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-30px) rotate(5deg);
  }
}

@keyframes backgroundShift {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

/* 响应式 */
@media (max-width: 992px) {
  .home-page {
    .hero-section {
      .hero-content {
        .hero-title {
          font-size: 3rem;
        }

        .hero-description {
          font-size: 1.25rem;
        }
      }

      .social-sidebar {
        display: none;
      }
    }

    .services-section {
      .section-header {
        grid-template-columns: 1fr;
        gap: 30px;
      }
    }
  }
}

@media (max-width: 768px) {
  .home-page {
    .hero-section {
      min-height: calc(100vh - 70px);

      .hero-content {
        .hero-title {
          font-size: 2.5rem;
        }

        .hero-description {
          font-size: 1.125rem;
        }

        .hero-actions {
          flex-direction: column;

          .btn {
            width: 100%;
            max-width: 300px;
          }
        }
      }
    }

    .services-section {
      .services-grid {
        grid-template-columns: 1fr;
      }
    }

    .cta-section {
      padding: 60px 0;

      .cta-content {
        h3 {
          font-size: 2rem;
        }

        p {
          font-size: 1.125rem;
        }
      }
    }
  }
}
</style>
