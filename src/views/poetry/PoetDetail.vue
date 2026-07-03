<template>
  <div class="poet-detail-page">
    <div class="container">
      <button class="btn-back" @click="goBack">← 返回</button>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
      
      <div v-else-if="poet" class="poet-content">
        <!-- 诗人信息卡片 -->
        <div class="poet-card" data-aos="fade-up">
          <div class="poet-avatar">{{ poet.name.charAt(0) }}</div>
          <h1>{{ poet.name }}</h1>
          <div class="meta">
            <span class="dynasty">{{ poet.dynasty }}</span>
            <span v-if="poet.zi" class="zi">字：{{ poet.zi }}</span>
            <span v-if="poet.hao" class="hao">号：{{ poet.hao }}</span>
          </div>
          <p class="years" v-if="poet.birth_year">
            {{ poet.birth_year }} - {{ poet.death_year || '?' }}
          </p>
          <div class="bio">{{ poet.bio }}</div>
          <div class="stats">
            <div class="stat-item">
              <span class="label">作品数量</span>
              <span class="value">{{ poet.poem_count }}</span>
            </div>
            <div class="stat-item">
              <span class="label">质量评分</span>
              <span class="value">{{ poet.quality_score }}</span>
            </div>
            <div class="stat-item">
              <span class="label">浏览量</span>
              <span class="value">{{ poet.view_count }}</span>
            </div>
          </div>
        </div>
        
        <!-- 诗人作品 -->
        <div class="poet-poems" data-aos="fade-up">
          <h2>代表作品</h2>
          <div class="poems-grid">
            <div
              v-for="poem in poems"
              :key="poem.id"
              class="poem-card"
              @click="goToPoem(poem.id)"
            >
              <h3>{{ poem.title }}</h3>
              <p class="content">{{ truncate(poem.content) }}</p>
              <div class="poem-stats">
                <span>❤️ {{ poem.like_count }}</span>
                <span>👁️ {{ poem.view_count }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { poetryApi, type Poet, type Poem } from '@/api/poetry'

const route = useRoute()
const router = useRouter()

const poet = ref<Poet | null>(null)
const poems = ref<Poem[]>([])
const loading = ref(true)

const loadPoet = async () => {
  loading.value = true
  const poetId = Number(route.params.id)
  
  try {
    poet.value = await poetryApi.getPoet(poetId)
    const result = await poetryApi.getPoetPoems(poetId, { per_page: 12 })
    poems.value = result.items
  } catch (error) {
    console.error('加载诗人详情失败:', error)
  } finally {
    loading.value = false
  }
}

const truncate = (text: string) => {
  return text.length > 60 ? text.substring(0, 60) + '...' : text
}

const goBack = () => {
  router.push({ name: 'PoetList' })
}

const goToPoem = (poemId: number) => {
  router.push({ name: 'PoetryDetail', params: { id: poemId } })
}

onMounted(() => {
  loadPoet()
})
</script>

<style scoped>
.poet-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40px 20px;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
}

.btn-back {
  background: white;
  color: #667eea;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: all 0.3s;
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

.poet-card {
  background: white;
  border-radius: 20px;
  padding: 50px;
  text-align: center;
  margin-bottom: 30px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.poet-avatar {
  width: 120px;
  height: 120px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  font-weight: bold;
}

.poet-card h1 {
  font-size: 36px;
  color: #2c3e50;
  margin-bottom: 15px;
}

.meta {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.meta span {
  padding: 6px 16px;
  border-radius: 15px;
  font-size: 14px;
}

.dynasty {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.zi, .hao {
  background: #ecf0f1;
  color: #2c3e50;
}

.years {
  color: #7f8c8d;
  margin-bottom: 20px;
  font-size: 16px;
}

.bio {
  line-height: 1.8;
  color: #34495e;
  margin-bottom: 30px;
  text-align: left;
}

.stats {
  display: flex;
  justify-content: space-around;
  padding-top: 30px;
  border-top: 2px solid #ecf0f1;
}

.stat-item {
  text-align: center;
}

.stat-item .label {
  display: block;
  font-size: 12px;
  color: #95a5a6;
  margin-bottom: 5px;
}

.stat-item .value {
  display: block;
  font-size: 28px;
  font-weight: bold;
  color: #667eea;
}

.poet-poems {
  background: white;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.poet-poems h2 {
  font-size: 28px;
  color: #2c3e50;
  margin-bottom: 30px;
  text-align: center;
}

.poems-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.poem-card {
  background: #f8f9fa;
  border-radius: 15px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s;
}

.poem-card:hover {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  transform: translateY(-5px);
}

.poem-card h3 {
  font-size: 18px;
  margin-bottom: 10px;
}

.poem-card .content {
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 15px;
  font-family: "KaiTi", "楷体", serif;
}

.poem-stats {
  display: flex;
  gap: 15px;
  font-size: 12px;
  opacity: 0.8;
}

@media (max-width: 768px) {
  .poet-card {
    padding: 30px 20px;
  }
  
  .poems-grid {
    grid-template-columns: 1fr;
  }
}
</style>
