/**
 * Page Engine 类型定义
 *
 * 与 api-contract.yaml 对齐，是前端消费的 TypeScript 类型来源。
 * 后续可通过 openapi-typescript 从 api-contract.yaml 自动生成替代本文件。
 *
 * @module page-engine.types
 */

// ============================================================
// 基础枚举
// ============================================================

/** 产品标识 — 多产品共享同一套引擎的关键 */
export type ProductId = 'jobfirst' | 'genzer' | 'blog' | 'szbolent'

/** 可见性级别 — 组件/页面/菜单项的访问控制粒度 */
export type VisibilityLevel =
  | 'public'          // 所有人可见
  | 'authenticated'   // 已登录
  | 'subscriber'      // 订阅者/关注者
  | 'friend'          // 好友
  | 'employer'        // 雇主/HR
  | 'admin'           // 管理员
  | 'vc_verified'     // 通过 VC 认证

/** 页面类型 — 影响布局和预加载策略 */
export type PageType = 'landing' | 'detail' | 'list' | 'dashboard' | 'form' | 'custom'

/** 布局模板 — 对应前端的 Layout 组件 */
export type LayoutType = 'default' | 'fullWidth' | 'sidebar' | 'cardGrid' | 'split'

/** 页面内容状态 */
export type PageStatus = 'draft' | 'published' | 'archived'

/** Casbin 操作 */
export type CasbinAction = 'read' | 'write' | 'delete' | 'manage'

// ============================================================
// 页面组件
// ============================================================

/** 组件类型 — 对应前端 componentMap 的 key */
export type ComponentType =
  | 'Hero'
  | 'Skills'
  | 'Timeline'
  | 'Contact'
  | 'Gallery'
  | 'Stats'
  | 'Form'
  | 'List'
  | 'Card'
  | 'Chart'
  | 'RichText'
  | 'Markdown'
  | 'CodeBlock'
  | 'Embed'

/** 页面组件定义 — 数据库中存储的最小渲染单元 */
export interface PageComponent {
  /** 组件在页面内的唯一标识 */
  id: string

  /** 组件类型名，对应前端 componentMap */
  type: ComponentType

  /** 传给组件的 props，结构由组件自身定义 */
  props: Record<string, unknown>

  /** 独立数据源 API 路径。为空则使用页面的全局 dataSource */
  dataSource?: string | null

  /** 可见性控制 */
  visibleTo: VisibilityLevel

  /** 配合 visibleTo='role' 使用 */
  requiredRole?: string | null

  /** 需要的 VC 凭证类型 (YeDall DID) */
  requiredVC?: string | null

  /** 组件在页面内的排列顺序 */
  order: number
}

// ============================================================
// 页面 Schema
// ============================================================

/** 页面 Schema — 一个完整页面的声明式定义 */
export interface PageSchema {
  id: string
  product: ProductId
  path: string
  pageType: PageType
  layout: LayoutType
  title: string
  description?: string | null
  visibility: VisibilityLevel

  /** 页面默认数据源 API */
  dataSource?: string | null

  /** SEO 元信息 */
  meta?: {
    ogImage?: string
    ogType?: string
    canonical?: string
  }

  /** 页面包含的组件列表（按 order 排序） */
  components: PageComponent[]

  status: PageStatus
  createdAt: string
  updatedAt: string
}

// ============================================================
// 菜单 / 路由
// ============================================================

/** 菜单节点 — 数据库存储的路由/菜单定义 */
export interface MenuItem {
  id: number
  parentId: number | null
  title: string
  path: string
  icon?: string | null
  component?: string | null
  visibleTo: VisibilityLevel
  requiredPermission?: string | null
  sortOrder: number
  product: ProductId
  children?: MenuItem[]

  /** 路由 meta 信息 */
  meta?: {
    keepAlive?: boolean
    requiresAuth?: boolean
    [key: string]: unknown
  }
}

// ============================================================
// 权限
// ============================================================

/** 权限策略 — 映射到 Casbin policy */
export interface PermissionPolicy {
  product: ProductId
  subject: string        // Casbin subject（用户 ID 或角色名）
  action: CasbinAction   // Casbin action
  resource: string       // Casbin object（资源路径）
}

/** 权限检查请求 */
export interface PermissionCheckRequest {
  resource: string
  action: CasbinAction
}

/** 权限检查响应 */
export interface PermissionCheckResponse {
  allowed: boolean
  reason?: string | null
}

// ============================================================
// API 响应包装
// ============================================================

/** 分页响应 */
export interface PaginatedResponse<T> {
  data: T[]
  total: number
}

/** API 错误 */
export interface ApiError {
  error: string
  message: string
}

// ============================================================
// 前端运行时类型
// ============================================================

/** 当前用户信息（简化版，对接 YeDall DID） */
export interface UserContext {
  id: string
  did?: string               // YeDall DID
  roles: string[]            // Casbin 角色列表
  vcs: string[]              // 持有的 VC 凭证类型
  isAuthenticated: boolean
}

/** 已解析的前端路由项（useDynamicRouter 产出） */
export interface ResolvedRoute {
  path: string
  name?: string
  component: () => Promise<unknown>
  meta: {
    title: string
    icon?: string
    requiresAuth?: boolean
    visibleTo: VisibilityLevel
    requiredPermission?: string
    [key: string]: unknown
  }
  children?: ResolvedRoute[]
}

/** 页面渲染上下文（传给每个 PageComponent） */
export interface PageRenderContext {
  page: PageSchema
  user: UserContext
  /** 页面级 dataSource 返回的数据 */
  pageData: Record<string, unknown> | null
  /** 当前组件在页面中的索引 */
  componentIndex: number
}

/** 组件动态 Props 工厂 */
export type ComponentPropsFactory = (
  comp: PageComponent,
  ctx: PageRenderContext
) => Record<string, unknown>

// ============================================================
// 组件映射表
// ============================================================

/** 组件注册表 — 组件类型名到 Vue 组件的映射 */
export type ComponentRegistry = Record<
  ComponentType,
  () => Promise<{ default: unknown }>
>
