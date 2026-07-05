<template>
  <div class="activity-detail-page">
    <div class="container">
      <button class="btn-back" @click="goBack">← 返回活动列表</button>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
      
      <div v-else-if="activity" class="activity-content">
        <!-- 活动信息 -->
        <div class="activity-header" data-aos="fade-down">
          <div class="icon">
            <span v-if="activity.type === 1">🎡</span>
            <span v-else-if="activity.type === 2">🎫</span>
            <span v-else>🥚</span>
          </div>
          <h1>{{ activity.name }}</h1>
          <p class="description">{{ activity.description }}</p>
          <div class="meta">
            <span class="type-badge">{{ getTypeName(activity.type) }}</span>
            <span class="time">
              {{ formatDate(activity.start_time) }} - {{ formatDate(activity.end_time) }}
            </span>
          </div>
        </div>
        
        <!-- 抽奖次数信息 -->
        <div class="draw-info" data-aos="fade-up">
          <div class="info-card">
            <div class="label">剩余次数</div>
            <div class="value">{{ drawCount }}</div>
          </div>
          <div class="info-card">
            <div class="label">周期限制</div>
            <div class="value">每{{ getLimitText(activity.limit_type) }}{{ activity.limit_times }}次</div>
          </div>
        </div>
        
        <!-- 抽奖区域 -->
        <div class="draw-area" data-aos="zoom-in">
          <div v-if="activity.type === 1" class="lucky-wheel-wrapper">
            <LuckyWheelComponent
              :activity-id="activity.id"
              :prizes="activity.prizes"
              :user-id="userId"
              @draw-complete="handleDrawComplete"
            />
          </div>
          
          <div v-else-if="activity.type === 2" class="scratch-card-wrapper">
            <div class="coming-soon">
              <h3>🎫 刮刮乐</h3>
              <p>敬请期待...</p>
            </div>
          </div>
          
          <div v-else class="golden-egg-wrapper">
            <div class="coming-soon">
              <h3>🥚 砸金蛋</h3>
              <p>敬请期待...</p>
            </div>
          </div>
        </div>
        
        <!-- 奖品列表 -->
        <div class="prizes-section" data-aos="fade-up">
          <h2>🎁 奖品列表</h2>
          <div class="prizes-grid">
            <div
              v-for="(prize, index) in activity.prizes"
              :key="index"
              class="prize-card"
            >
              <div class="prize-icon">
                {{ prize.type === 0 ? '🎁' : '💎' }}
              </div>
              <div class="prize-name">{{ prize.name }}</div>
              <div class="prize-percent">中奖率: {{ prize.percent }}%</div>
              <div class="prize-num">剩余: {{ prize.num }}</div>
            </div>
          </div>
        </div>
        
        <!-- 获取次数方式 -->
        <div class="get-chances" data-aos="fade-up">
          <h3>💡 如何获取抽奖次数？</h3>
          <ul>
            <li>📖 阅读诗词，每读一首诗词获得 1 次机会</li>
            <li>❤️ 点赞诗词，每次点赞获得 1 次机会</li>
            <li>📤 分享诗词，每次分享获得 2 次机会</li>
            <li>✍️ 发表评论，每条评论获得 1 次机会</li>
          </ul>
          <button class="btn-go-poetry" @click="goToPoetry">
            去读诗词 →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { activityApi, type Activity } from '@/api/activity'
import LuckyWheelComponent from '@/components/LuckyWheel.vue'

const route = useRoute()
const router = useRouter()

const activity = ref<Activity | null>(null)
const drawCount = ref(0)
const loading = ref(true)
const userId = ref(1) // 实际应从用户系统获取

const loadActivity = async () => {
  loading.value = true
  const activityId = Number(route.params.id)
  
  try {
    activity.value = await activityApi.getDetail(activityId)
    const countResult = await activityApi.getDrawCount(activityId, userId.value)
    drawCount.value = countResult.remaining
  } catch (error) {
    console.error('加载活动详情失败:', error)
    alert('加载失败，请检查后端服务')
  } finally {
    loading.value = false
  }
}

const handleDrawComplete = async () => {
  // 抽奖完成后刷新次数
  if (activity.value) {
    const countResult = await activityApi.getDrawCount(activity.value.id, userId.value)
    drawCount.value = countResult.remaining
  }
}

const getTypeName = (type: 1 | 2 | 3) => {
  const names = { 1: '大转盘', 2: '刮刮乐', 3: '砸金蛋' }
  return names[type]
}

const getLimitText = (limitType: string) => {
  const texts: any = {
    day: '日',
    week: '周',
    month: '月',
    year: '年'
  }
  return texts[limitType] || '日'
}

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr)
  return `${date.getFullYear()}/${date.getMonth() + 1}/${date.getDate()}`
}

const goBack = () => {
  router.push({ name: 'ActivityList' })
}

const goToPoetry = () => {
  router.push({ name: 'PoetryList' })
}

onMounted(() => {
  loadActivity()
})
</script>

<style scoped>
.activity-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  padding: 40px 20px;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
}

.btn-back {
  background: white;
  color: #f5576c;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: all 0.3s;
}

.btn-back:hover {
  transform: translateX(-5px);
}

.loading {
  text-align: center;
  color: white;
  padding: 100px 0;
}

.spinner {
  width: 50px;
  height: 50px;
  margin: 0 auto 20px;
  border: 5px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.activity-header {
  background: white;
  border-radius: 25px;
  padding: 50px;
  text-align: center;
  margin-bottom: 30px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.icon {
  font-size: 100px;
  margin-bottom: 20px;
}

.activity-header h1 {
  font-size: 42px;
  color: #2c3e50;
  margin-bottom: 15px;
}

.description {
  color: #7f8c8d;
  font-size: 18px;
  margin-bottom: 25px;
}

.meta {
  display: flex;
  justify-content: center;
  gap: 20px;
  flex-wrap: wrap;
}

.type-badge {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
  padding: 10px 25px;
  border-radius: 25px;
  font-weight: bold;
}

.time {
  color: #95a5a6;
  padding: 10px 20px;
  background: var(--bolent-bg-soft);
  border-radius: 20px;
}

.draw-info {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.info-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.info-card .label {
  font-size: 14px;
  color: #95a5a6;
  margin-bottom: 10px;
}

.info-card .value {
  font-size: 32px;
  font-weight: bold;
  color: #f5576c;
}

.draw-area {
  background: white;
  border-radius: 25px;
  padding: 40px;
  margin-bottom: 30px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.coming-soon {
  text-align: center;
  padding: 80px 20px;
  color: #95a5a6;
}

.coming-soon h3 {
  font-size: 36px;
  margin-bottom: 15px;
}

.prizes-section {
  background: white;
  border-radius: 25px;
  padding: 40px;
  margin-bottom: 30px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.prizes-section h2 {
  font-size: 28px;
  color: #2c3e50;
  margin-bottom: 30px;
  text-align: center;
}

.prizes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.prize-card {
  background: var(--bolent-bg-soft);
  border-radius: 15px;
  padding: 25px;
  text-align: center;
  transition: all 0.3s;
}

.prize-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.prize-icon {
  font-size: 48px;
  margin-bottom: 10px;
}

.prize-name {
  font-size: 18px;
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 10px;
}

.prize-percent {
  font-size: 14px;
  color: #7f8c8d;
  margin-bottom: 5px;
}

.prize-num {
  font-size: 12px;
  color: #95a5a6;
}

.get-chances {
  background: white;
  border-radius: 25px;
  padding: 40px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.get-chances h3 {
  font-size: 24px;
  color: #2c3e50;
  margin-bottom: 20px;
  text-align: center;
}

.get-chances ul {
  list-style: none;
  padding: 0;
  margin-bottom: 30px;
}

.get-chances li {
  padding: 15px 20px;
  background: var(--bolent-bg-soft);
  border-radius: 10px;
  margin-bottom: 10px;
  font-size: 16px;
  color: #34495e;
}

.btn-go-poetry {
  display: block;
  width: 100%;
  background: var(--bolent-gradient);
  color: white;
  border: none;
  padding: 15px;
  border-radius: 25px;
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-go-poetry:hover {
  transform: scale(1.02);
  box-shadow: 0 10px 30px rgba(14, 110, 106, 0.4);
}

@media (max-width: 768px) {
  .activity-header {
    padding: 30px 20px;
  }
  
  .draw-info {
    grid-template-columns: 1fr;
  }
  
  .prizes-grid {
    grid-template-columns: 1fr;
  }
}
</style>
