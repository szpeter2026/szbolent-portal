import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import('@/views/Home.vue'),
        meta: { title: '首页' }
      },
      {
        path: 'about',
        name: 'About',
        component: () => import('@/views/About.vue'),
        meta: { title: '关于我们' }
      },
      {
        path: 'services',
        name: 'Services',
        component: () => import('@/views/Services.vue'),
        meta: { title: '我们的服务' }
      },
      {
        path: 'services/:slug',
        name: 'ServiceDetail',
        component: () => import('@/views/ServiceDetail.vue'),
        meta: { title: '服务详情' }
      },
      {
        path: 'blog',
        name: 'blog',
        component: () => import('@/views/Blog.vue'),
        meta: { title: '博客' }
      },
      {
        path: 'blog/:slug',
        name: 'blog-detail',
        component: () => import('@/views/BlogDetail.vue'),
        meta: { title: '博客详情' }
      },
      {
        path: 'case-study',
        name: 'CaseStudy',
        component: () => import('@/views/CaseStudy.vue'),
        meta: { title: '案例研究' }
      },
      {
        path: 'case-study/:slug',
        name: 'CaseStudyDetail',
        component: () => import('@/views/CaseStudyDetail.vue'),
        meta: { title: '案例详情' }
      },
      {
        path: 'careers',
        name: 'Careers',
        component: () => import('@/views/Careers.vue'),
        meta: { title: '加入我们' }
      },
      {
        path: 'contact',
        name: 'Contact',
        component: () => import('@/views/Contact.vue'),
        meta: { title: '联系我们' }
      },
      // 诗词专区路由
      {
        path: 'poetry',
        name: 'Poetry',
        component: () => import('@/views/poetry/Layout.vue'),
        meta: { title: '诗词鉴赏' },
        children: [
          {
            path: '',
            name: 'PoetryList',
            component: () => import('@/views/poetry/List.vue'),
            meta: { title: '诗词列表' }
          },
          {
            path: ':id',
            name: 'PoetryDetail',
            component: () => import('@/views/poetry/Detail.vue'),
            meta: { title: '诗词详情' }
          },
          {
            path: 'poets',
            name: 'PoetList',
            component: () => import('@/views/poetry/PoetList.vue'),
            meta: { title: '诗人列表' }
          },
          {
            path: 'poets/:id',
            name: 'PoetDetail',
            component: () => import('@/views/poetry/PoetDetail.vue'),
            meta: { title: '诗人详情' }
          }
        ]
      },
      {
        path: 'pricing',
        name: 'Pricing',
        component: () => import('@/views/Pricing.vue'),
        meta: { title: '定价方案' }
      },
      {
        path: 'privacy',
        name: 'Privacy',
        component: () => import('@/views/Privacy.vue'),
        meta: { title: '隐私政策' }
      },
      {
        path: 'terms',
        name: 'Terms',
        component: () => import('@/views/Terms.vue'),
        meta: { title: '用户协议' }
      },
      // 活动抽奖路由 — 已禁用：依赖的 legacy Sanic :8001 已退役
      // 待 Looma 后端活动 API 就绪后重新启用
      // {
      //   path: 'activity',
      //   name: 'Activity',
      //   component: () => import('@/views/activity/Layout.vue'),
      //   meta: { title: '活动中心' },
      //   children: [
      //     {
      //       path: '',
      //       name: 'ActivityList',
      //       component: () => import('@/views/activity/List.vue'),
      //       meta: { title: '活动列表' }
      //     },
      //     {
      //       path: ':id',
      //       name: 'ActivityDetail',
      //       component: () => import('@/views/activity/Detail.vue'),
      //       meta: { title: '活动详情' }
      //     }
      //   ]
      // }
    ]
  },

  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue'),
    meta: { title: '404' }
  }
]

import { seoConfig } from '@/config/company'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  }
})

// 路由守卫
router.beforeEach((to, from, next) => {
  const page = (to.meta.title as string) || seoConfig.defaultTitle
  document.title = seoConfig.titleTemplate.replace('%s', page)
  next()
})

export default router
