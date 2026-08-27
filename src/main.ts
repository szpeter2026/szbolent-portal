import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router, { menusInit } from './router'

// 导入 AOS 动画库
import AOS from 'aos'
import 'aos/dist/aos.css'

// 导入全局样式
import './assets/styles/main.scss'

async function bootstrap() {
  // 等待 Page Engine 动态路由加载完成后再安装 Router
  // 路由首次导航由 app.use(router) 触发，必须在此之前完成路由注入
  await menusInit

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
}

bootstrap()
