/**
 * usePageRenderer — Schema 驱动的页面渲染引擎
 *
 * 职责：
 * 1. 根据当前路由加载对应的 PageSchema（GET /v1/pages/by-path）
 * 2. 按权限过滤组件列表
 * 3. 从 dataSource 加载页面数据
 * 4. 输出渲染上下文，供 PageRenderer.vue 消费
 *
 * 使用：
 *   const { pageSchema, visibleComponents, pageData, isLoading } = usePageRenderer()
 *
 * @module composables/usePageRenderer
 */

import { ref, computed, watch, type Ref } from 'vue'
import { useRoute } from 'vue-router'
import { apiGet } from '@/api/looma'
import { usePermission } from './usePermission'
import type {
  PageSchema,
  PageComponent,
  PageRenderContext,
  ProductId
} from '@/api/page-engine.types'

// ============================================================
// 缓存
// ============================================================

/** 已加载的页面 Schema 缓存（path → schema） */
const schemaCache = new Map<string, PageSchema>()

/** 已加载的页面数据缓存（dataSource → data） */
const dataCache = new Map<string, Record<string, unknown>>()

// ============================================================
// Composable
// ============================================================

export function usePageRenderer(product?: ProductId) {
  const route = useRoute()
  const { isVisible, user } = usePermission()

  // 状态
  const pageSchema: Ref<PageSchema | null> = ref(null)
  const pageData: Ref<Record<string, unknown> | null> = ref(null)
  const isLoading: Ref<boolean> = ref(false)
  const error: Ref<string | null> = ref(null)

  /**
   * 根据当前路由路径加载页面 Schema
   */
  async function loadPageSchema(customPath?: string): Promise<void> {
    const pagePath = customPath || route.path

    // 命中缓存
    if (schemaCache.has(pagePath)) {
      pageSchema.value = schemaCache.get(pagePath)!
      await loadPageData()
      return
    }

    isLoading.value = true
    error.value = null

    try {
      const query = product ? `?product=${product}` : ''
      const schema = await apiGet<PageSchema>(
        `/pages/by-path?path=${encodeURIComponent(pagePath)}${query}`
      )

      pageSchema.value = schema
      schemaCache.set(pagePath, schema)

      // 加载页面数据
      await loadPageData()
    } catch (err: any) {
      if (err?.status === 404) {
        error.value = '页面不存在'
      } else {
        error.value = err?.message || '页面加载失败'
      }
      pageSchema.value = null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * 从 dataSource 加载页面数据
   */
  async function loadPageData(): Promise<void> {
    const schema = pageSchema.value
    if (!schema?.dataSource) return

    // 命中缓存
    if (dataCache.has(schema.dataSource)) {
      pageData.value = dataCache.get(schema.dataSource)!
      return
    }

    try {
      const data = await apiGet<Record<string, unknown>>(schema.dataSource)
      pageData.value = data
      dataCache.set(schema.dataSource, data)
    } catch {
      console.warn('[PageRenderer] Failed to load dataSource:', schema.dataSource)
      pageData.value = null
    }
  }

  /**
   * 可见组件列表（按 order 排序，已过滤不可见组件）
   */
  const visibleComponents = computed<PageComponent[]>(() => {
    if (!pageSchema.value) return []

    return pageSchema.value.components
      .filter(comp => {
        return isVisible(
          comp.visibleTo || pageSchema.value!.visibility,
          comp.requiredRole,
          comp.requiredVC
        )
      })
      .sort((a, b) => a.order - b.order)
  })

  /**
   * 渲染上下文（传给每个组件的完整环境）
   */
  const renderContext = computed<PageRenderContext | null>(() => {
    if (!pageSchema.value) return null

    return {
      page: pageSchema.value,
      user: user.value,
      pageData: pageData.value,
      componentIndex: 0  // 由渲染过程更新
    }
  })

  /**
   * 页面标题
   */
  const pageTitle = computed<string>(() => {
    return pageSchema.value?.title || ''
  })

  /**
   * 页面描述
   */
  const pageDescription = computed<string>(() => {
    return pageSchema.value?.description || ''
  })

  /**
   * SEO meta（供 <Head> 组件消费）
   */
  const seoMeta = computed(() => {
    if (!pageSchema.value?.meta) return {}
    return {
      ogTitle: pageSchema.value.title,
      ogDescription: pageSchema.value.description,
      ...pageSchema.value.meta
    }
  })

  // ============================================================
  // 路由变化监听
  // ============================================================

  watch(
    () => route.path,
    (newPath) => {
      if (newPath) {
        loadPageSchema(newPath)
      }
    }
  )

  // ============================================================
  // 工具方法
  // ============================================================

  /**
   * 清空指定路径的缓存（页面更新后调用）
   */
  function clearCache(pagePath?: string): void {
    if (pagePath) {
      schemaCache.delete(pagePath)
    } else {
      schemaCache.clear()
      dataCache.clear()
    }
  }

  /**
   * 手动设置页面 Schema（用于无需接口的场景，如静态页面）
   */
  function setPageSchema(schema: PageSchema): void {
    pageSchema.value = schema
    schemaCache.set(schema.path, schema)
  }

  return {
    // 状态
    pageSchema,
    pageData,
    isLoading,
    error,

    // 计算属性
    visibleComponents,
    renderContext,
    pageTitle,
    pageDescription,
    seoMeta,

    // 方法
    loadPageSchema,
    loadPageData,
    clearCache,
    setPageSchema
  }
}
