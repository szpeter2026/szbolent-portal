<template>
  <div class="poetry-list-page">
    <div class="container">
      <!-- 顶部横幅 -->
      <div class="page-header" data-aos="fade-down">
        <h1>诗词鉴赏</h1>
        <p>品味千年文化，感受诗词之美</p>
      </div>
      
      <!-- 筛选器 -->
      <div class="filters" data-aos="fade-up">
        <div class="filter-group">
          <label>朝代：</label>
          <button
            v-for="d in dynasties"
            :key="d"
            :class="{ active: dynasty === d }"
            @click="selectDynasty(d)"
          >
            {{ d || '全部' }}
          </button>
        </div>
        
        <div class="filter-group">
          <label>季节：</label>
          <button
            v-for="s in seasons"
            :key="s"
            :class="{ active: season === s }"
            @click="selectSeason(s)"
          >
            {{ s || '全部' }}
          </button>
        </div>
        
        <div class="filter-group">
          <label>主题：</label>
          <button
            v-for="t in themes"
            :key="t"
            :class="{ active: theme === t }"
            @click="selectTheme(t)"
          >
            {{ t || '全部' }}
          </button>
        </div>
      </div>
      
      <!-- 诗词网格 -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
      
      <div v-else class="poetry-grid">
        <div
          v-for="(poem, index) in poems"
          :key="poem.id"
          class="poetry-card"
          :data-aos="'fade-up'"
          :data-aos-delay="index * 50"
          @click="goToDetail(poem.id)"
        >
          <div class="card-header">
            <h3>{{ poem.title }}</h3>
            <span class="dynasty-badge">{{ poem.dynasty }}</span>
          </div>
          
          <div class="card-meta">
            <span class="author">{{ poem.author.name }}</span>
          </div>
          
          <div class="card-content">
            {{ truncateContent(poem.content) }}
          </div>
          
          <div class="card-footer">
            <div class="stats">
              <span>❤️ {{ poem.like_count }}</span>
              <span>👁️ {{ poem.view_count }}</span>
            </div>
            <div class="quality">
              质量分: {{ poem.quality_score }}
            </div>
          </div>
        </div>
      </div>
      
      <!-- 分页 -->
      <div v-if="!loading && totalPages > 1" class="pagination" data-aos="fade-up">
        <button
          :disabled="page === 1"
          @click="prevPage"
        >
          上一页
        </button>
        <span>第 {{ page }} / {{ totalPages }} 页</span>
        <button
          :disabled="page === totalPages"
          @click="nextPage"
        >
          下一页
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { poetryApi, type Poem } from '@/api/poetry'

const router = useRouter()

// 筛选选项
const dynasties = ['', '先秦', '汉', '魏晋', '南北朝', '隋', '唐', '宋', '元', '明', '清']
const seasons = ['', '春', '夏', '秋', '冬']
const themes = ['', '边塞', '送别', '山水', '田园', '咏史', '咏物', '爱情', '哲理']

// 筛选条件
const dynasty = ref('')
const season = ref('')
const theme = ref('')
const page = ref(1)
const perPage = ref(20)

// 数据
const poems = ref<Poem[]>([])
const totalPages = ref(0)
const loading = ref(false)

// 加载诗词列表
const loadPoems = async () => {
  loading.value = true
  
  try {
    const params: any = {
      page: page.value,
      per_page: perPage.value,
      sort_by: 'quality_score',
      order: 'desc' as const
    }
    
    if (dynasty.value) params.dynasty = dynasty.value
    if (season.value) params.season = season.value
    if (theme.value) params.theme = theme.value
    
    const result = await poetryApi.getPoems(params)
    
    poems.value = result.items
    totalPages.value = result.total_pages
  } catch (error) {
    console.error('加载诗词失败:', error)
    alert('加载诗词失败，请检查后端服务是否启动')
  } finally {
    loading.value = false
  }
}

// 筛选操作
const selectDynasty = (d: string) => {
  dynasty.value = d
  page.value = 1
  loadPoems()
}

const selectSeason = (s: string) => {
  season.value = s
  page.value = 1
  loadPoems()
}

const selectTheme = (t: string) => {
  theme.value = t
  page.value = 1
  loadPoems()
}

// 分页操作
const prevPage = () => {
  if (page.value > 1) {
    page.value--
    loadPoems()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const nextPage = () => {
  if (page.value < totalPages.value) {
    page.value++
    loadPoems()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// 截断内容
const truncateContent = (content: string) => {
  return content.length > 80 ? content.substring(0, 80) + '...' : content
}

// 跳转到详情页
const goToDetail = (id: number) => {
  router.push({ name: 'PoetryDetail', params: { id } })
}

onMounted(() => {
  loadPoems()
})
</script>

<style scoped>
.poetry-list-page {
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

.page-header p {
  font-size: 18px;
  opacity: 0.9;
}

.filters {
  background: white;
  border-radius: 20px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.filter-group {
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.filter-group:last-child {
  margin-bottom: 0;
}

.filter-group label {
  font-weight: bold;
  margin-right: 10px;
  min-width: 60px;
}

.filter-group button {
  padding: 8px 16px;
  border: 2px solid #ecf0f1;
  background: white;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
}

.filter-group button:hover {
  border-color: var(--bolent-primary);
  color: var(--bolent-primary);
}

.filter-group button.active {
  background: var(--bolent-gradient);
  color: white;
  border-color: transparent;
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

.poetry-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.poetry-card {
  background: white;
  border-radius: 20px;
  padding: 25px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.poetry-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.card-header h3 {
  font-size: 22px;
  color: #2c3e50;
  flex: 1;
  margin: 0;
}

.dynasty-badge {
  background: var(--bolent-gradient);
  color: white;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
}

.card-meta {
  color: #7f8c8d;
  margin-bottom: 15px;
  font-size: 14px;
}

.card-content {
  font-size: 16px;
  line-height: 1.8;
  color: #34495e;
  white-space: pre-line;
  margin-bottom: 15px;
  font-family: "KaiTi", "楷体", serif;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid #ecf0f1;
}

.stats {
  display: flex;
  gap: 15px;
  font-size: 14px;
  color: #7f8c8d;
}

.quality {
  font-size: 12px;
  color: #95a5a6;
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
  font-size: 14px;
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
  .poetry-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header h1 {
    font-size: 32px;
  }
  
  .filters {
    padding: 20px;
  }
  
  .filter-group {
    font-size: 12px;
  }
}
</style>
