/**
 * WordPress REST API 工具
 */

const WP_API_BASE = import.meta.env.VITE_BLOG_API_BASE || '/wp-json/wp/v2'

export interface WPPost {
  id: number
  date: string
  date_gmt: string
  modified: string
  slug: string
  status: string
  type: string
  link: string
  title: {
    rendered: string
  }
  content: {
    rendered: string
    protected: boolean
  }
  excerpt: {
    rendered: string
    protected: boolean
  }
  author: number
  featured_media: number
  comment_status: string
  categories: number[]
  tags: number[]
  _embedded?: {
    author?: Array<{
      id: number
      name: string
      description: string
      avatar_urls: Record<string, string>
    }>
    'wp:featuredmedia'?: Array<{
      id: number
      source_url: string
      alt_text: string
      media_details: {
        width: number
        height: number
      }
    }>
    'wp:term'?: Array<Array<{
      id: number
      name: string
      slug: string
    }>>
  }
}

export interface WPCategory {
  id: number
  count: number
  description: string
  link: string
  name: string
  slug: string
  parent: number
}

export interface WPTag {
  id: number
  count: number
  description: string
  link: string
  name: string
  slug: string
}

export interface PostsQuery {
  page?: number
  per_page?: number
  search?: string
  categories?: number[]
  tags?: number[]
  author?: number
  orderby?: 'date' | 'relevance' | 'id' | 'title'
  order?: 'asc' | 'desc'
}

/**
 * 获取文章列表
 */
export async function getPosts(query: PostsQuery = {}): Promise<{
  posts: WPPost[]
  total: number
  totalPages: number
}> {
  const params = new URLSearchParams()
  
  // 默认参数
  params.append('_embed', '1') // 包含作者、特色图片等信息
  params.append('per_page', String(query.per_page || 10))
  params.append('page', String(query.page || 1))
  
  if (query.search) params.append('search', query.search)
  if (query.categories?.length) params.append('categories', query.categories.join(','))
  if (query.tags?.length) params.append('tags', query.tags.join(','))
  if (query.author) params.append('author', String(query.author))
  if (query.orderby) params.append('orderby', query.orderby)
  if (query.order) params.append('order', query.order)

  try {
    const response = await fetch(`${WP_API_BASE}/posts?${params.toString()}`)
    
    if (!response.ok) {
      throw new Error(`WordPress API 错误: ${response.status}`)
    }

    const posts = await response.json()
    const total = parseInt(response.headers.get('X-WP-Total') || '0')
    const totalPages = parseInt(response.headers.get('X-WP-TotalPages') || '0')

    return {
      posts,
      total,
      totalPages
    }
  } catch (error) {
    console.error('获取文章列表失败:', error)
    throw error
  }
}

/**
 * 获取单篇文章（通过 ID）
 */
export async function getPostById(id: number): Promise<WPPost> {
  try {
    const response = await fetch(`${WP_API_BASE}/posts/${id}?_embed=1`)
    
    if (!response.ok) {
      throw new Error(`WordPress API 错误: ${response.status}`)
    }

    return await response.json()
  } catch (error) {
    console.error('获取文章详情失败:', error)
    throw error
  }
}

/**
 * 获取单篇文章（通过 slug）
 */
export async function getPostBySlug(slug: string): Promise<WPPost | null> {
  try {
    const response = await fetch(`${WP_API_BASE}/posts?slug=${slug}&_embed=1`)
    
    if (!response.ok) {
      throw new Error(`WordPress API 错误: ${response.status}`)
    }

    const posts = await response.json()
    return posts.length > 0 ? posts[0] : null
  } catch (error) {
    console.error('获取文章详情失败:', error)
    throw error
  }
}

/**
 * 获取分类列表
 */
export async function getCategories(): Promise<WPCategory[]> {
  try {
    const response = await fetch(`${WP_API_BASE}/categories?per_page=100`)
    
    if (!response.ok) {
      throw new Error(`WordPress API 错误: ${response.status}`)
    }

    return await response.json()
  } catch (error) {
    console.error('获取分类列表失败:', error)
    throw error
  }
}

/**
 * 获取标签列表
 */
export async function getTags(): Promise<WPTag[]> {
  try {
    const response = await fetch(`${WP_API_BASE}/tags?per_page=100`)
    
    if (!response.ok) {
      throw new Error(`WordPress API 错误: ${response.status}`)
    }

    return await response.json()
  } catch (error) {
    console.error('获取标签列表失败:', error)
    throw error
  }
}

/**
 * 格式化日期
 */
export function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleDateString('zh-CN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

/**
 * 提取纯文本摘要（移除 HTML 标签）
 */
export function extractExcerpt(html: string, maxLength = 150): string {
  const text = html.replace(/<[^>]*>/g, '').trim()
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

/**
 * 基础 HTML 消毒 — 防 XSS
 * 移除 script/iframe/object/embed/link/style 标签和事件处理器
 * 生产环境建议升级为 DOMPurify
 */
export function sanitizeHTML(html: string): string {
  if (!html) return ''

  // 移除危险标签及其内容
  html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
  html = html.replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '')
  html = html.replace(/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/gi, '')
  html = html.replace(/<embed\b[^<]*>/gi, '')
  html = html.replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '')

  // 移除危险属性：on* handlers, javascript: 协议, vbscript:
  html = html.replace(/\son\w+\s*=\s*["'][^"']*["']/gi, '')
  html = html.replace(/\son\w+\s*=\s*[^\s>]*/gi, '')
  html = html.replace(/href\s*=\s*["']javascript:/gi, 'href="#"')
  html = html.replace(/src\s*=\s*["']javascript:/gi, 'src="#"')
  html = html.replace(/vbscript:/gi, '')

  // 移除空 HTML 注释（WP 常见残留）
  html = html.replace(/<!--[\s]*-->/g, '')

  return html
}
