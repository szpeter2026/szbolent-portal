<template>
  <div class="poetry-detail-page">
    <div class="container">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
      
      <div v-else-if="poem" class="detail-content">
        <!-- 返回按钮 -->
        <button class="btn-back" @click="goBack">
          ← 返回列表
        </button>
        
        <!-- 诗词主体 -->
        <article class="poem-article" data-aos="fade-up">
          <header class="poem-header">
            <h1>{{ poem.title }}</h1>
            <div class="meta">
              <span class="dynasty">{{ poem.dynasty }}</span>
              <span class="author" @click="goToPoet(poem.author.id)">
                {{ poem.author.name }}
              </span>
              <span v-if="poem.season" class="season">{{ poem.season }}</span>
              <span v-if="poem.theme" class="theme">{{ poem.theme }}</span>
            </div>
          </header>
          
          <div class="poem-content">
            {{ poem.content }}
          </div>
          
          <!-- 译文 -->
          <div v-if="poem.translation" class="translation" data-aos="fade-up">
            <h3>白话翻译</h3>
            <div class="text">{{ poem.translation }}</div>
          </div>
          
          <!-- 赏析 -->
          <div v-if="poem.appreciation" class="appreciation" data-aos="fade-up">
            <h3>诗词赏析</h3>
            <div class="text">{{ poem.appreciation }}</div>
          </div>
          
          <!-- 互动操作 -->
          <div class="actions" data-aos="fade-up">
            <button 
              class="btn-like" 
              :class="{ liked: hasLiked }"
              :disabled="hasLiked"
              @click="handleLike"
            >
              ❤️ {{ hasLiked ? '已点赞' : '点赞' }} ({{ poem.like_count }})
            </button>
            <button class="btn-share" @click="handleShare">
              📤 分享
            </button>
            <button class="btn-collect">
              ⭐ 收藏 ({{ poem.favorite_count }})
            </button>
          </div>
          
          <!-- 统计信息 -->
          <div class="stats-bar" data-aos="fade-up">
            <div class="stat-item">
              <span class="label">浏览量</span>
              <span class="value">{{ poem.view_count }}</span>
            </div>
            <div class="stat-item">
              <span class="label">质量分</span>
              <span class="value">{{ poem.quality_score }}</span>
            </div>
            <div class="stat-item">
              <span class="label">热度</span>
              <span class="value">{{ poem.popularity_score }}</span>
            </div>
          </div>
        </article>
      </div>
      
      <div v-else class="error">
        <p>诗词不存在或加载失败</p>
        <button @click="goBack">返回列表</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { poetryApi, type Poem } from '@/api/poetry'
import { activityApi } from '@/api/activity'

const route = useRoute()
const router = useRouter()

const poem = ref<Poem | null>(null)
const loading = ref(true)
const hasLiked = ref(false)

// 加载诗词详情
const loadPoem = async () => {
  loading.value = true
  const poemId = Number(route.params.id)
  
  try {
    poem.value = await poetryApi.getPoem(poemId)
    
    // 检查本地存储是否已点赞
    const likedPoems = JSON.parse(localStorage.getItem('likedPoems') || '[]')
    hasLiked.value = likedPoems.includes(poemId)
  } catch (error) {
    console.error('加载诗词详情失败:', error)
  } finally {
    loading.value = false
  }
}

// 点赞
const handleLike = async () => {
  if (!poem.value || hasLiked.value) return
  
  try {
    await poetryApi.likePoem(poem.value.id)
    poem.value.like_count++
    hasLiked.value = true
    
    // 保存到本地存储
    const likedPoems = JSON.parse(localStorage.getItem('likedPoems') || '[]')
    likedPoems.push(poem.value.id)
    localStorage.setItem('likedPoems', JSON.stringify(likedPoems))
    
    // 发放抽奖次数（如果有活动）
    try {
      // 假设用户ID为1（实际应从用户系统获取）
      await activityApi.grant(1, 1, 1) // 活动ID=1，用户ID=1，发放1次
    } catch (error) {
      console.log('发放抽奖次数失败或无活动')
    }
    
    alert('点赞成功！')
  } catch (error) {
    console.error('点赞失败:', error)
    alert('点赞失败，请重试')
  }
}

// 分享
const handleShare = () => {
  if (!poem.value) return
  
  const shareText = `${poem.value.title} - ${poem.value.author.name}\n${poem.value.content}`
  
  if (navigator.share) {
    navigator.share({
      title: poem.value.title,
      text: shareText,
      url: window.location.href
    }).then(() => {
      // 分享成功，发放抽奖次数
      activityApi.grant(2, 1, 1).catch(() => {}) // 活动ID=2
    })
  } else {
    // 复制到剪贴板
    navigator.clipboard.writeText(shareText).then(() => {
      alert('已复制到剪贴板')
    })
  }
}

// 返回
const goBack = () => {
  router.push({ name: 'PoetryList' })
}

// 跳转到诗人详情
const goToPoet = (poetId: number) => {
  router.push({ name: 'PoetDetail', params: { id: poetId } })
}

onMounted(() => {
  loadPoem()
})
</script>

<style scoped>
.poetry-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40px 20px;
}

.container {
  max-width: 900px;
  margin: 0 auto;
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

.btn-back {
  background: white;
  color: #667eea;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: all 0.3s;
  font-size: 14px;
}

.btn-back:hover {
  transform: translateX(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.poem-article {
  background: white;
  border-radius: 20px;
  padding: 50px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.poem-header {
  text-align: center;
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 2px solid #ecf0f1;
}

.poem-header h1 {
  font-size: 42px;
  color: #2c3e50;
  margin-bottom: 20px;
  font-weight: bold;
}

.meta {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}

.meta span {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
}

.dynasty {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.author {
  background: #ecf0f1;
  color: #2c3e50;
  cursor: pointer;
  transition: all 0.3s;
}

.author:hover {
  background: #3498db;
  color: white;
}

.season, .theme {
  background: #f8f9fa;
  color: #7f8c8d;
}

.poem-content {
  font-size: 24px;
  line-height: 2.5;
  color: #2c3e50;
  white-space: pre-line;
  text-align: center;
  margin-bottom: 40px;
  font-family: "KaiTi", "楷体", serif;
  padding: 30px 0;
}

.translation, .appreciation {
  background: #f8f9fa;
  border-radius: 15px;
  padding: 30px;
  margin-bottom: 30px;
}

.translation h3, .appreciation h3 {
  font-size: 20px;
  color: #2c3e50;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.translation h3::before {
  content: '📖';
}

.appreciation h3::before {
  content: '✨';
}

.text {
  font-size: 16px;
  line-height: 1.8;
  color: #34495e;
  white-space: pre-line;
}

.actions {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin: 40px 0;
  flex-wrap: wrap;
}

.actions button {
  padding: 12px 30px;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s;
}

.btn-like {
  background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
  color: white;
}

.btn-like:not(:disabled):hover {
  transform: scale(1.05);
  box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
}

.btn-like.liked {
  background: #95a5a6;
  cursor: not-allowed;
}

.btn-share {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-share:hover {
  transform: scale(1.05);
  box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.btn-collect {
  background: linear-gradient(135deg, #ffd93d, #ffb638);
  color: white;
}

.btn-collect:hover {
  transform: scale(1.05);
  box-shadow: 0 5px 20px rgba(255, 217, 61, 0.4);
}

.stats-bar {
  display: flex;
  justify-content: space-around;
  padding: 30px 0;
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
  font-size: 24px;
  font-weight: bold;
  color: #667eea;
}

.error {
  text-align: center;
  color: white;
  padding: 100px 0;
}

.error p {
  font-size: 24px;
  margin-bottom: 20px;
}

.error button {
  background: white;
  color: #667eea;
  border: none;
  padding: 12px 30px;
  border-radius: 25px;
  cursor: pointer;
  font-size: 16px;
}

@media (max-width: 768px) {
  .poem-article {
    padding: 30px 20px;
  }
  
  .poem-header h1 {
    font-size: 28px;
  }
  
  .poem-content {
    font-size: 18px;
  }
  
  .actions {
    flex-direction: column;
  }
  
  .actions button {
    width: 100%;
  }
}
</style>
