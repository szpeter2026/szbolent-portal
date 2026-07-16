<template>
  <div class="pricing-page">
    <section class="pricing-hero">
      <h1>选择适合您的方案</h1>
      <p class="subtitle">解锁诗词鉴赏与 AI 问答的全部潜力</p>
    </section>

    <section class="pricing-grid">
      <div
        v-for="plan in pricingPlans"
        :key="plan.id"
        :class="['pricing-card', { highlighted: plan.highlighted }]"
      >
        <div v-if="plan.highlighted" class="popular-badge">最受欢迎</div>
        <h2>{{ plan.name }}</h2>
        <div class="price">
          <span class="amount">¥{{ plan.price }}</span>
          <span class="period">/{{ plan.period }}</span>
        </div>
        <ul class="features">
          <li v-for="(feature, idx) in plan.features" :key="idx">
            <span class="check">✓</span> {{ feature }}
          </li>
        </ul>
        <button :class="['cta-btn', { primary: plan.highlighted }]">
          {{ plan.cta }}
        </button>
      </div>
    </section>

    <section class="pricing-note">
      <p>所有方案均包含 7 天免费试用。无需信用卡即可开始。</p>
      <p>需要企业定制方案？<a href="/contact">联系我们</a></p>
    </section>
  </div>
</template>

<script setup lang="ts">
import { pricingPlans } from '@/config/company'
</script>

<style scoped>
.pricing-page { max-width: 1100px; margin: 0 auto; padding: 3rem 1rem; }

.pricing-hero { text-align: center; margin-bottom: 3rem; }
.pricing-hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
.subtitle { color: #666; font-size: 1.1rem; }

.pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }

.pricing-card {
  position: relative;
  background: #fff;
  border: 2px solid #eee;
  border-radius: 12px;
  padding: 2rem 1.5rem;
  text-align: center;
  transition: transform 0.2s;
}
.pricing-card:hover { transform: translateY(-4px); }
.pricing-card.highlighted { border-color: #4a90d9; box-shadow: 0 4px 20px rgba(74,144,217,0.15); }

.popular-badge {
  position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
  background: #4a90d9; color: #fff; padding: 0.25rem 1rem; border-radius: 20px;
  font-size: 0.8rem; font-weight: 600;
}

.pricing-card h2 { font-size: 1.3rem; margin-bottom: 1rem; }

.price { margin-bottom: 1.5rem; }
.amount { font-size: 2.5rem; font-weight: 700; color: #333; }
.period { color: #999; font-size: 0.9rem; }

.features { list-style: none; padding: 0; margin: 0 0 1.5rem 0; text-align: left; }
.features li { padding: 0.4rem 0; color: #555; font-size: 0.95rem; }
.check { color: #4caf50; margin-right: 0.5rem; font-weight: 700; }

.cta-btn {
  width: 100%; padding: 0.75rem; border: 2px solid #ddd; border-radius: 8px;
  background: #fff; font-size: 1rem; cursor: pointer; transition: all 0.2s;
}
.cta-btn:hover { border-color: #999; }
.cta-btn.primary { background: #4a90d9; color: #fff; border-color: #4a90d9; }
.cta-btn.primary:hover { background: #357abd; }

.pricing-note { text-align: center; color: #888; font-size: 0.9rem; line-height: 1.8; }
.pricing-note a { color: #4a90d9; }
</style>