import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// 导入 AOS 动画库
import AOS from 'aos'
import 'aos/dist/aos.css'

// 导入全局样式
import './assets/styles/main.scss'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// 初始化 AOS 动画
AOS.init({
  duration: 800,
  easing: 'ease-in-out',
  once: true,
  mirror: false
})

app.mount('#app')
