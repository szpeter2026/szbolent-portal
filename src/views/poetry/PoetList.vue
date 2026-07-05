<template>
  <div class="poet-list-page">
    <div class="container">
      <div class="page-header" data-aos="fade-down">
        <h1>诗人列表</h1>
        <p>探索历代诗词大家的人生轨迹</p>
      </div>
      
      <!-- 朝代筛选 -->
      <div class="filters" data-aos="fade-up">
        <button
          v-for="d in dynasties"
          :key="d"
          :class="{ active: dynasty === d }"
          @click="selectDynasty(d)"
        >
          {{ d || '全部' }}
        </button>
      </div>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
      
      <div v-else class="poets-grid">
        <div
          v-for="(poet, index) in poets"
          :key="poet.id"
          class="poet-card"
          :data-aos="'zoom-in'"
          :data-aos-delay="index * 50"
          @click="goToPoet(poet.id)"
        >
          <div class="poet-avatar">
            {{ poet.name.charAt(0) }}
          </div>
          <h3>{{ poet.name }}</h3>
          <p class="dynasty">{{ poet.dynasty }}</p>
          <p class="years" v-if="poet.birth_year">
            {{ poet.birth_year }} - {{ poet.death_year || '?' }}
          </p>
          <div class="stats">
            <span>📜 {{ poet.poem_count }} 首</span>
            <span>⭐ {{ poet.quality_score }}</span>
          </div>
        </div>
      </div>
      
      <!-- 分页 -->
      <div v-if="!loading && totalPages > 1" class="pagination" data-aos="fade-up">
        <button :disabled="page === 1" @click="prevPage">上一页</button>
        <span>第 {{ page }} / {{ totalPages }} 页</span>
        <button :disabled="page === totalPages" @click="nextPage">下一页</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { poetryApi, type Poet } from '@/api/poetry'

const router = useRouter()

const dynasties = ['', '先秦', '汉', '魏晋', '南北朝', '隋', '唐', '宋', '元', '明', '清']
const dynasty = ref('')
const page = ref(1)
const poets = ref<Poet[]>([])
const totalPages = ref(0)
const loading = ref(false)

const loadPoets = async () => {
  loading.value = true
  
  try {
    const params: any = {
      page: page.value,
      per_page: 24
    }
    
    if (dynasty.value) params.dynasty = dynasty.value
    
    const result = await poetryApi.getPoets(params)
    poets.value = result.items
    totalPages.value = result.total_pages
  } catch (error) {
    console.error('加载诗人失败:', error)
    alert('加载诗人失败，请检查后端服务')
  } finally {
    loading.value = false
  }
}

const selectDynasty = (d: string) => {
  dynasty.value = d
  page.value = 1
  loadPoets()
}

const prevPage = () => {
  if (page.value > 1) {
    page.value--
    loadPoets()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const nextPage = () => {
  if (page.value < totalPages.value) {
    page.value++
    loadPoets()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToPoet = (poetId: number) => {
  router.push({ name: 'PoetDetail', params: { id: poetId } })
}

onMounted(() => {
  loadPoets()
})
</script>

<style scoped>
.poet-list-page {
  min-height: 100vh;
  background: var(--bolent-gradient);
  padding: 40px 20px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  text-align: center;
  color: white;
  margin-bottom: 40px;
}

.page-header h1 {
  font-size: 48px;
  margin-bottom: 10px;
  font-weight: bold;
}

.filters {
  display: flex;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 40px;
}

.filters button {
  padding: 10px 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s;
}

.filters button:hover {
  background: rgba(255, 255, 255, 0.2);
}

.filters button.active {
  background: white;
  color: var(--bolent-primary);
  border-color: white;
}

.loading {
  text-align: center;
  color: white;
  padding: 60px 0;
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

.poets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.poet-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.poet-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

.poet-avatar {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  background: var(--bolent-gradient);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  font-weight: bold;
}

.poet-card h3 {
  font-size: 24px;
  color: #2c3e50;
  margin-bottom: 10px;
}

.dynasty {
  color: #7f8c8d;
  margin-bottom: 5px;
}

.years {
  color: #95a5a6;
  font-size: 14px;
  margin-bottom: 15px;
}

.stats {
  display: flex;
  justify-content: center;
  gap: 20px;
  padding-top: 15px;
  border-top: 1px solid #ecf0f1;
  font-size: 14px;
  color: #7f8c8d;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  padding: 30px 0;
}

.pagination button {
  padding: 10px 20px;
  background: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s;
}

.pagination button:not(:disabled):hover {
  background: var(--bolent-gradient);
  color: white;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination span {
  color: white;
  font-weight: bold;
}

@media (max-width: 768px) {
  .poets-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
  
  .page-header h1 {
    font-size: 32px;
  }
}
</style>
