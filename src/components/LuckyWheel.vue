<template>
  <div class="lucky-wheel-container">
    <div class="wheel-wrapper">
      <canvas
        ref="canvasRef"
        :width="canvasSize"
        :height="canvasSize"
        @click="handleSpin"
      ></canvas>
      <div class="pointer">▼</div>
    </div>
    
    <div class="remaining-count">
      剩余抽奖次数: <span class="count">{{ localDrawCount }}</span>
    </div>
    
    <button
      class="btn-spin"
      :disabled="spinning || localDrawCount <= 0"
      @click="handleSpin"
    >
      {{ spinning ? '抽奖中...' : '开始抽奖' }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { activityApi } from '@/api/activity'

interface Prize {
  name: string
  num: number
  percent: number
  type: 0 | 1
}

const props = defineProps<{
  activityId: number
  userId: number
  prizes: Prize[]
}>()

const emit = defineEmits<{
  (e: 'draw-complete'): void
}>()

const canvasRef = ref<HTMLCanvasElement | null>(null)
const canvasSize = ref(500)
const spinning = ref(false)
const localDrawCount = ref(0)
const currentAngle = ref(0)
const targetAngle = ref(0)

// 颜色配置
const colors = ['#FFD700', '#FFA500', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4']

// 获取抽奖次数
const fetchDrawCount = async () => {
  try {
    const result = await activityApi.getDrawCount(props.activityId, props.userId)
    localDrawCount.value = result.remaining
  } catch (error) {
    console.error('获取抽奖次数失败:', error)
  }
}

// 绘制转盘
const drawWheel = () => {
  const canvas = canvasRef.value
  if (!canvas) return
  
  const ctx = canvas.getContext('2d')
  if (!ctx) return
  
  const centerX = canvasSize.value / 2
  const centerY = canvasSize.value / 2
  const radius = canvasSize.value / 2 - 20
  const prizeCount = props.prizes.length
  const anglePerPrize = (2 * Math.PI) / prizeCount
  
  // 清空画布
  ctx.clearRect(0, 0, canvasSize.value, canvasSize.value)
  
  // 保存当前状态
  ctx.save()
  
  // 移动到中心点并旋转
  ctx.translate(centerX, centerY)
  ctx.rotate(currentAngle.value)
  
  // 绘制每个扇形
  props.prizes.forEach((prize, index) => {
    const startAngle = index * anglePerPrize - Math.PI / 2
    const endAngle = (index + 1) * anglePerPrize - Math.PI / 2
    
    // 绘制扇形
    ctx.beginPath()
    ctx.moveTo(0, 0)
    ctx.arc(0, 0, radius, startAngle, endAngle)
    ctx.closePath()
    ctx.fillStyle = colors[index % colors.length]
    ctx.fill()
    
    // 绘制边框
    ctx.strokeStyle = 'white'
    ctx.lineWidth = 3
    ctx.stroke()
    
    // 绘制文字
    ctx.save()
    ctx.rotate(startAngle + anglePerPrize / 2)
    ctx.textAlign = 'center'
    ctx.fillStyle = 'white'
    ctx.font = 'bold 18px Arial'
    ctx.fillText(prize.name, radius / 2, 10)
    ctx.restore()
  })
  
  // 绘制中心圆
  ctx.beginPath()
  ctx.arc(0, 0, 40, 0, 2 * Math.PI)
  ctx.fillStyle = '#FF6B6B'
  ctx.fill()
  ctx.strokeStyle = 'white'
  ctx.lineWidth = 3
  ctx.stroke()
  
  ctx.fillStyle = 'white'
  ctx.font = 'bold 14px Arial'
  ctx.textAlign = 'center'
  ctx.fillText('抽奖', 0, 5)
  
  // 恢复状态
  ctx.restore()
}

// 旋转动画
const animateRotation = () => {
  const diff = targetAngle.value - currentAngle.value
  
  if (Math.abs(diff) > 0.01) {
    currentAngle.value += diff * 0.1
    drawWheel()
    requestAnimationFrame(animateRotation)
  } else {
    currentAngle.value = targetAngle.value
    drawWheel()
    spinning.value = false
    
    // 显示中奖结果
    const prizeCount = props.prizes.length
    const anglePerPrize = (2 * Math.PI) / prizeCount
    const normalizedAngle = (currentAngle.value % (2 * Math.PI) + 2 * Math.PI) % (2 * Math.PI)
    const prizeIndex = Math.floor(normalizedAngle / anglePerPrize)
    const prize = props.prizes[prizeIndex]
    
    setTimeout(() => {
      alert(`🎉 恭喜获得: ${prize.name}`)
      emit('draw-complete')
    }, 100)
  }
}

// 开始抽奖
const handleSpin = async () => {
  if (spinning.value || localDrawCount.value <= 0) return
  
  spinning.value = true
  
  try {
    // 调用抽奖API
    const result = await activityApi.draw(props.activityId, props.userId)
    const prizeIndex = result.prize_index - 1 // API返回1-based索引
    
    // 计算目标角度（多转几圈增加效果）
    const prizeCount = props.prizes.length
    const anglePerPrize = (2 * Math.PI) / prizeCount
    const extraRotations = 5 // 额外转5圈
    targetAngle.value = currentAngle.value + 
                       extraRotations * 2 * Math.PI + 
                       (prizeIndex * anglePerPrize) + 
                       anglePerPrize / 2
    
    // 开始动画
    animateRotation()
    
    // 更新剩余次数
    localDrawCount.value = result.remaining
    
  } catch (error: any) {
    console.error('抽奖失败:', error)
    alert(error.response?.data?.message || '抽奖失败，请重试')
    spinning.value = false
  }
}

// 监听props变化
watch(() => props.prizes, () => {
  drawWheel()
}, { deep: true })

onMounted(() => {
  fetchDrawCount()
  drawWheel()
  
  // 响应式调整
  if (window.innerWidth < 768) {
    canvasSize.value = 350
  }
})
</script>

<style scoped>
.lucky-wheel-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 30px;
  padding: 20px;
}

.wheel-wrapper {
  position: relative;
  display: inline-block;
}

canvas {
  cursor: pointer;
  filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.2));
}

.pointer {
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 40px;
  color: #FF6B6B;
  filter: drop-shadow(0 3px 5px rgba(0, 0, 0, 0.3));
  pointer-events: none;
}

.remaining-count {
  font-size: 24px;
  font-weight: bold;
  color: #2c3e50;
}

.remaining-count .count {
  color: #FF6B6B;
  font-size: 32px;
}

.btn-spin {
  padding: 15px 50px;
  font-size: 20px;
  font-weight: bold;
  color: white;
  background: linear-gradient(135deg, #FF6B6B, #ee5a6f);
  border: none;
  border-radius: 30px;
  cursor: pointer;
  transition: var(--bolent-transition);
  box-shadow: 0 5px 20px rgba(255, 107, 107, 0.3);
}

.btn-spin:not(:disabled):hover {
  transform: scale(1.05);
  box-shadow: 0 8px 30px rgba(255, 107, 107, 0.5);
}

.btn-spin:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

@media (max-width: 768px) {
  canvas {
    max-width: 100%;
    height: auto;
  }
  
  .remaining-count {
    font-size: 18px;
  }
  
  .remaining-count .count {
    font-size: 24px;
  }
  
  .btn-spin {
    padding: 12px 40px;
    font-size: 18px;
  }
}
</style>
