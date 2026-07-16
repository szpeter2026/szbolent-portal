<template>
  <div class="case-detail-page" v-if="caseItem">
    <section class="case-detail-hero">
      <div class="container">
        <router-link to="/case-study" class="back-link">&larr; 返回案例列表</router-link>
        <span class="industry-tag">{{ caseItem.industry }}</span>
        <h1>{{ caseItem.title }}</h1>
        <p class="case-desc">{{ caseItem.description }}</p>
        <div class="stats-row">
          <div class="stat-item" v-for="stat in caseItem.stats" :key="stat.label">
            <span class="stat-value">{{ stat.value }}</span>
            <span class="stat-label">{{ stat.label }}</span>
          </div>
        </div>
      </div>
    </section>

    <section class="case-detail-body">
      <div class="container">
        <h2>项目背景</h2>
        <p>{{ caseItem.description }}</p>
        <p>我们与客户密切合作，深入了解业务需求，制定了针对性的技术解决方案。通过敏捷开发方法和持续交付实践，确保项目按时高质量交付。</p>

        <h2>解决方案</h2>
        <ul>
          <li>采用微服务架构设计，确保系统的高可用性和可扩展性</li>
          <li>实施自动化 CI/CD 流水线，提升交付效率</li>
          <li>建立完善的监控和告警体系，保障系统稳定运行</li>
          <li>提供持续的技术支持和运维服务</li>
        </ul>

        <h2>项目成果</h2>
        <img :src="caseItem.image" :alt="caseItem.title" class="case-image" />
        <p><router-link to="/contact" class="cta-link">对类似方案感兴趣？联系我们 &rarr;</router-link></p>
      </div>
    </section>
  </div>

  <div class="case-detail-page" v-else>
    <div class="container" style="text-align:center;padding:100px 0;">
      <h1>案例未找到</h1>
      <router-link to="/case-study">返回案例列表</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const cases = [
  { id: 1, slug: 'fintech-payment-platform', title: '某支付平台核心系统重构', description: '为国内领先的第三方支付平台重构核心交易系统，实现高并发处理能力和系统稳定性的大幅提升。', industry: '金融科技', service: 'IT 外包', image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop', stats: [{ label: '并发处理提升', value: '300%' }, { label: '系统稳定性', value: '99.99%' }, { label: '响应时间降低', value: '60%' }] },
  { id: 2, slug: 'ecommerce-omnichannel', title: '全渠道电商平台建设', description: '为某知名零售品牌打造全渠道电商平台，整合线上线下资源，实现会员数据统一管理。', industry: '电商零售', service: '定制开发', image: 'https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?w=800&h=600&fit=crop', stats: [{ label: '用户增长', value: '150%' }, { label: '转化率提升', value: '80%' }, { label: '客单价增长', value: '45%' }] },
  { id: 3, slug: 'healthcare-appointment-system', title: '智能预约挂号系统', description: '为三甲医院开发智能预约挂号系统，优化就医流程，提升患者体验和医院运营效率。', industry: '医疗健康', service: '敏捷咨询', image: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=600&fit=crop', stats: [{ label: '预约效率提升', value: '200%' }, { label: '患者满意度', value: '95%' }, { label: '运营成本降低', value: '40%' }] },
  { id: 4, slug: 'education-learning-platform', title: '在线学习平台开发', description: '为教育机构打造AI驱动的在线学习平台，实现个性化学习路径推荐和智能作业批改。', industry: '在线教育', service: '定制开发', image: 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=800&h=600&fit=crop', stats: [{ label: '学员增长', value: '250%' }, { label: '完课率提升', value: '70%' }, { label: '教学效率', value: '+120%' }] },
  { id: 5, slug: 'enterprise-crm-system', title: '企业 CRM 系统升级', description: '为大型企业升级 CRM 系统，整合销售、市场、客服数据，实现客户全生命周期管理。', industry: '企业服务', service: '数字化转型', image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop', stats: [{ label: '销售效率', value: '+180%' }, { label: '客户留存率', value: '92%' }, { label: 'ROI 提升', value: '320%' }] },
  { id: 6, slug: 'logistics-tracking-system', title: '物流追踪管理系统', description: '为物流公司开发实时追踪系统，实现货物全程可视化管理和智能调度优化。', industry: '物流配送', service: 'IT 外包', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop', stats: [{ label: '配送效率', value: '+160%' }, { label: '准时率', value: '98%' }, { label: '成本节约', value: '35%' }] }
]

const caseItem = computed(() => {
  return cases.find(c => c.slug === route.params.slug) || null
})
</script>

<style scoped>
.case-detail-hero { background: linear-gradient(135deg, var(--bolent-ink), #1a1a2e); padding: 80px 0 60px; color: white; }
.back-link { color: rgba(255,255,255,0.7); font-size: 14px; display: inline-block; margin-bottom: 20px; }
.back-link:hover { color: white; }
.industry-tag { display: inline-block; background: var(--bolent-accent-light, #7F77DD); color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px; margin-bottom: 16px; }
.case-detail-hero h1 { font-size: 2rem; margin-bottom: 16px; }
.case-desc { color: rgba(255,255,255,0.8); max-width: 700px; line-height: 1.8; }
.stats-row { display: flex; gap: 40px; margin-top: 30px; }
.stat-value { display: block; font-size: 1.5rem; font-weight: 500; }
.stat-label { display: block; font-size: 13px; color: rgba(255,255,255,0.6); margin-top: 4px; }

.case-detail-body { padding: 60px 0; }
.case-detail-body h2 { font-size: 1.3rem; margin-top: 40px; margin-bottom: 16px; }
.case-detail-body p { color: #555; line-height: 1.8; margin-bottom: 16px; }
.case-detail-body ul { padding-left: 1.5rem; margin-bottom: 24px; }
.case-detail-body li { color: #555; line-height: 2; }
.case-image { max-width: 100%; border-radius: 8px; margin: 20px 0; }
.cta-link { color: var(--bolent-accent-light, #7F77DD); font-weight: 500; }

@media (max-width: 768px) {
  .stats-row { flex-wrap: wrap; gap: 20px; }
  .case-detail-hero h1 { font-size: 1.5rem; }
}
</style>
