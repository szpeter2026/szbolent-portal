/// <reference types="vite/client" />

interface ImportMetaEnv {
  /** 门户公网地址（SEO / OG 用） */
  readonly VITE_SITE_URL: string
  /** Looma 商业/API 真源 */
  readonly VITE_LOOMA_API_BASE: string
  /** WordPress REST API 地址 */
  readonly VITE_BLOG_API_BASE: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
