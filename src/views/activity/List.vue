<template>
  <div class="activity-list-page">
    <div class="container">
      <div class="page-header" data-aos="fade-down">
        <h1>🎉 精彩活动</h1>
        <p>参与活动，赢取精美奖品</p>
      </div>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
      
      <div v-else class="activities-grid">
        <div
          v-for="(activity, index) in activities"
          :key="activity.id"
          class="activity-card"
          :data-aos="'flip-left'"
          :data-aos-delay="index * 100"
          @click="goToActivity(activity.id)"
        >
          <div class="card-icon">
            <span v-if="activity.type === 1">🎡</span>
            <span v-else-if="activity.type === 2">🎫</span>
            <span v-else>🥚</span>
          </div>
          <h3>{{ activity.name }}</h3>
          <p class="description">{{ activity.description }}</p>
          <div class="card-meta">
            <span class="type-badge">
              {{ getTypeName(activity.type) }}
            </span>
            <span class="time">
              {{ formatDate(activity.start_time) }} 至 {{ formatDate(activity.end_time) }}
            </span>
          </div>
          <div class="card-footer">
            <div class="limit">
              每{{ getLimitText(activity.limit_type) }}限{{ activity.limit_times }}次
            </div>
            <button class="btn-join">立即参与 →</button>
          </div>
        </div>
      </div>
      
      <div v-if="!loading && activities.length === 0" class="empty">
        <p>暂无活动</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { activityApi, type Activity } from '@/api/activity'

const router = useRouter()

const activities = ref<Activity[]>([])
const loading = ref(false)

const loadActivities = async () => {
  loading.value = true
  
  try {
    const result = await activityApi.getList()
    activities.value = result.data || []
  } catch (error) {
    console.error('加载活动失败:', error)
    alert('加载活动失败，请检查后端服务')
  } finally {
    loading.value = false
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
  return `${date.getMonth() + 1}/${date.getDate()}`
}

const goToActivity = (activityId: number) => {
  router.push({ name: 'ActivityDetail', params: { id: activityId } })
}

onMounted(() => {
  loadActivities()
})
</script>

<style scoped>
.activity-list-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  padding: 40px 20px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  text-align: center;
  color: white;
  margin-bottom: 50px;
}

.page-header h1 {
  font-size: 48px;
  margin-bottom: 10px;
  font-weight: bold;
}

.page-header p {
  font-size: 18px;
  opacity: 0.9;
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

.activities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 30px;
}

.activity-card {
  background: white;
  border-radius: 25px;
  padding: 40px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.activity-card:hover {
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.card-icon {
  font-size: 80px;
  text-align: center;
  margin-bottom: 20px;
}

.activity-card h3 {
  font-size: 28px;
  color: #2c3e50;
  margin-bottom: 15px;
  text-align: center;
}

.description {
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 20px;
  text-align: center;
  min-height: 48px;
}

.card-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 15px 0;
  border-top: 2px solid #ecf0f1;
  border-bottom: 2px solid #ecf0f1;
}

.type-badge {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: bold;
}

.time {
  font-size: 12px;
  color: #95a5a6;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.limit {
  font-size: 14px;
  color: #7f8c8d;
}

.btn-join {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 25px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  transition: all 0.3s;
}

.btn-join:hover {
  transform: scale(1.05);
  box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
}

.empty {
  text-align: center;
  color: white;
  padding: 100px 0;
  font-size: 24px;
}

@media (max-width: 768px) {
  .activities-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header h1 {
    font-size: 36px;
  }
  
  .activity-card {
    padding: 30px 20px;
  }
}
</style>
