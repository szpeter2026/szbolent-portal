/**
 * Looma API 客户端 — 身份认证 / 支付 / 企业服务
 *
 * 数据真源：looma-zervi `backend/src/api/routes/auth_routes.py` 等
 * 类型将在 S0 阶段从 OpenAPI 生成，当前用泛型薄封装。
 *
 * JWT token 存储键：looma_token（与 looma 前端保持一致，见 G2 门禁）
 */
import axios from 'axios'

/** 生产填 https://api.szbolent.com.cn；本地留空走 vite proxy /v1 → :5200 */
const LOOMA_BASE = (import.meta.env.VITE_LOOMA_API_BASE ?? '').replace(/\/$/, '')

/** HTTP 超时统一 30s（G2 门禁） */
const API_TIMEOUT_MS = 30000

/** looma JWT 存储键（与 planetx/saas 保持一致） */
const TOKEN_KEY = 'looma_token'

function v1(path: string): string {
  return `${LOOMA_BASE}/v1${path}`
}

// ── Token 管理 ──

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY)
}

export function setToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token)
}

export function clearToken(): void {
  localStorage.removeItem(TOKEN_KEY)
}

// ── Axios 实例 ──

const api = axios.create({
  timeout: API_TIMEOUT_MS,
})

// 请求拦截：自动带 JWT
api.interceptors.request.use((config) => {
  const token = getToken()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// 响应拦截：401 自动清 token（G2 门禁）
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      clearToken()
      // portal 消费端可监听此事件跳转登录
    }
    return Promise.reject(error)
  },
)

// ── 泛型请求封装（等 S0 契约后再替换为具体类型）──

/** GET 请求封装 */
export async function apiGet<T>(path: string, params?: Record<string, unknown>): Promise<T> {
  const { data } = await api.get<T>(v1(path), { params })
  return data
}

/** POST 请求封装 */
export async function apiPost<T>(path: string, body?: Record<string, unknown>): Promise<T> {
  const { data } = await api.post<T>(v1(path), body ?? {})
  return data
}

// ── 认证接口（薄封装，具体请求/响应体等 S0 契约定义）──

/** Web 登录 */
export async function login(email: string, password: string): Promise<{ token: string }> {
  const data = await apiPost<{ token: string }>('/auth/login', { email, password })
  if (data.token) setToken(data.token)
  return data
}

/** Web 注册 */
export function register(params: { email: string; password: string; name?: string }): Promise<{ token: string }> {
  return apiPost<{ token: string }>('/auth/register', params)
}

/** 获取当前用户信息 */
export function getMe<T = Record<string, unknown>>(): Promise<T> {
  return apiGet<T>('/auth/me')
}

/** 微信小程序登录绑定 */
export function wechatLogin(code: string): Promise<{ token: string }> {
  return apiPost<{ token: string }>('/auth/wechat', { code })
}

// ── 支付接口（P1 后接真实类型）──

/** 创建支付订单 */
export function createOrder(params: Record<string, unknown>): Promise<Record<string, unknown>> {
  return apiPost('/payment/create', params)
}

/** 查询支付状态 */
export function queryOrder(orderId: string): Promise<Record<string, unknown>> {
  return apiGet(`/payment/status/${orderId}`)
}