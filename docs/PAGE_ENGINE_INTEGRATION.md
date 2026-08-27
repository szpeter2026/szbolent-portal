/**
 * Page Engine 集成示例
 *
 * 展示 useDynamicRouter + usePageRenderer + usePermission + PermissionGuard
 * 如何替换当前静态路由，实现数据库驱动的页面渲染。
 *
 * 此文件为参考示例，不直接导入项目。
 *
 * @example 集成到 router/index.ts
 */

// ============================================================
// 示例 1：router/index.ts — 从静态路由迁移到动态路由
// ============================================================

/*
import { createRouter, createWebHistory } from 'vue-router'
import { useDynamicRouter } from '@/composables/useDynamicRouter'
import DefaultLayout from '@/layouts/DefaultLayout.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    // 保留少数静态路由（登录、404 等）
    { path: '/login',  component: () => import('@/views/LoginPage.vue') },
    { path: '/:pathMatch(.*)*', component: () => import('@/views/NotFound.vue') }
  ]
})

// 动态路由守卫
router.beforeEach(async (to, _from, next) => {
  const { loadMenus } = useDynamicRouter()

  try {
    await loadMenus(router, 'szbolent', () => import('@/layouts/DefaultLayout.vue'))
  } catch (err) {
    console.error('Failed to load dynamic routes:', err)
  }

  next()
})

export default router
*/

// ============================================================
// 示例 2：App.vue — 全局权限初始化
// ============================================================

/*
<script setup lang="ts">
import { onMounted } from 'vue'
import { usePermission } from '@/composables/usePermission'

const { initialize } = usePermission()

onMounted(async () => {
  await initialize()   // 拉取用户上下文
})
</script>
*/

// ============================================================
// 示例 3：任何 Vue 组件中使用权限控制
// ============================================================

/*
<template>
  <div>
    <!-- 公开内容：所有人都能看到 -->
    <h2>{{ resume.title }}</h2>

    <!-- 已登录才能看 -->
    <PermissionGuard visibleTo="authenticated">
      <SkillsSection :skills="resume.skills" />
    </PermissionGuard>

    <!-- 特定角色才能看 -->
    <PermissionGuard
      visibleTo="employer"
      requiredRole="hr_manager"
      :denied-message="'该部分仅对HR经理开放'"
    >
      <ContactSection :contact="resume.contact" />
    </PermissionGuard>

    <!-- 带 fallback -->
    <PermissionGuard
      visibleTo="vc_verified"
      requiredVC="employment_verification"
    >
      <template #default>
        <VerifiedBadge />
      </template>
      <template #fallback>
        <VerifyPrompt />
      </template>
    </PermissionGuard>
  </div>
</template>

<script setup lang="ts">
import { PermissionGuard } from '@/components/shared'
</script>
*/

// ============================================================
// 示例 4：Schema 驱动页面（替换 WordPress PHP 模板）
// ============================================================

/*
<template>
  <PageRenderer
    :componentMap="componentMap"
    product="szbolent"
  >
    <template #loading>
      <LoadingSkeleton />
    </template>
  </PageRenderer>
</template>

<script setup lang="ts">
import { PageRenderer } from '@/components/shared'
import type { ComponentRegistry } from '@/api/page-engine.types'

// 定义"组件白名单"——只有这里注册的组件才能被 Schema 引用
const componentMap: ComponentRegistry = {
  Hero:      () => import('@/views/components/HeroSection.vue'),
  Skills:    () => import('@/views/components/SkillsGrid.vue'),
  Timeline:  () => import('@/views/components/TimelineBlock.vue'),
  Contact:   () => import('@/views/components/ContactInfo.vue'),
  Card:      () => import('@/views/components/CardView.vue'),
  Gallery:   () => import('@/views/components/ImageGallery.vue'),
  Stats:     () => import('@/views/components/StatsPanel.vue'),
  Form:      () => import('@/views/components/DynamicForm.vue'),
  RichText:  () => import('@/views/components/RichContent.vue'),
  Markdown:  () => import('@/views/components/MarkdownView.vue'),
  List:      () => import('@/views/components/ItemList.vue'),
  Chart:     () => import('@/views/components/DataChart.vue'),
  CodeBlock: () => import('@/views/components/CodeBlock.vue'),
  Embed:     () => import('@/views/components/EmbedFrame.vue')
}
</script>
*/

// ============================================================
// 示例 5：数据库中的页面 Schema 示例（Blog 简历页）
// ============================================================

/*
POST /v1/pages
{
  "id": "b3e4f5a6-...",
  "product": "blog",
  "path": "/resume/zhangsan",
  "pageType": "detail",
  "layout": "sidebar",
  "title": "张三的简历",
  "description": "全栈工程师 · Rust & Vue",
  "visibility": "public",
  "dataSource": "/v1/blog/user/zhangsan",
  "components": [
    {
      "id": "hero",
      "type": "Hero",
      "props": {
        "name": "张三",
        "title": "全栈工程师",
        "avatar": "https://..."
      },
      "visibleTo": "public",
      "order": 0
    },
    {
      "id": "skills",
      "type": "Skills",
      "props": {
        "categories": ["前端", "后端", "DevOps"]
      },
      "visibleTo": "public",
      "order": 1
    },
    {
      "id": "experience",
      "type": "Timeline",
      "props": {
        "title": "工作经历"
      },
      "dataSource": "/v1/blog/user/zhangsan/experience",
      "visibleTo": "authenticated",
      "order": 2
    },
    {
      "id": "contact",
      "type": "Contact",
      "props": {
        "showEmail": true,
        "showPhone": false
      },
      "visibleTo": "employer",
      "requiredRole": "hr_manager",
      "order": 3
    }
  ],
  "status": "published"
}
*/

// ============================================================
// 示例 6：AI 通过 MCP Sidecar 生成页面 Schema
// ============================================================

/*
// MCP Tool 定义（在 Looma 的 MCP Sidecar 中）
{
  "name": "create_page",
  "description": "创建或更新一个页面 Schema。路由、组件、权限全部由这份 JSON 定义。",
  "parameters": {
    "product": "blog",
    "path": "/blog/my-new-page",
    "description": "一个技术博客文章页，包含标题、正文、评论区",
    "visibility": "public",
    "layout": "default",
    "components": [
      { "type": "Hero",    "visibleTo": "public" },
      { "type": "RichText","visibleTo": "public" },
      { "type": "Form",    "visibleTo": "authenticated" }
    ]
  }
}

// AI 调用 MCP → MCP 调用 POST /v1/pages → 页面即时上线
// 前端无需任何代码变更
*/
