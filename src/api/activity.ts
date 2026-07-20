/**
 * ⚠️ ARCHIVED — 活动抽奖 API 接口
 *
 * @deprecated 此模块依赖已下线的 legacy bolent Sanic (:8001)。
 *   looma-zervi 尚未迁移活动路由，当前**所有函数不可用**。
 *   待决策后：
 *     - 方案A: 迁移到 looma /v1/activity/* 并改用 apiPost/apiGet
 *     - 方案B: portal 下线活动模块（推荐，优先级低于诗词核心）
 *
 * @see DUAL_REPO_WORK_GUIDE.md §6.1 P0-L6
 * @todo 迁移或删除 — 2026 Q3 评估
 */
import axios from 'axios'

/** legacy bolent Sanic 地址（已下线）；迁移后改为 import.meta.env.VITE_LOOMA_API_BASE + '/v1/activity' */
const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8001'

/** ⚠️ 运行时警告：防止生产环境误调用已归档的 activity API */
const _ARCHIVED_WARNING = '[activity.ts] 此模块已 ARCHIVED，依赖的下线服务 localhost:8001 不可用。如需活动功能，请先迁移至 looma /v1/activity/*。'

if (import.meta.env.PROD) {
  console.warn(_ARCHIVED_WARNING)
}

export interface Activity {
  id: number
  type: 1 | 2 | 3  // 1=大转盘 2=刮刮乐 3=砸金蛋
  name: string
  description: string
  start_time: string
  end_time: string
  limit_type: 'day' | 'week' | 'month' | 'year'
  limit_times: number
  user_score: number
  prize_num_show: number
  prizes: Array<{
    name: string
    num: number
    percent: number
    type: 0 | 1  // 0=实物 1=虚拟
  }>
}

export interface DrawCountResponse {
  activity_id: number
  user_id: number
  period_key: string
  remaining: number
  limit_times: number
}

export interface DrawResponse {
  activity_id: number
  prize_index: number
  prize_name: string
  remaining: number
}

export interface GrantResponse {
  activity_id: number
  user_id: number
  granted: number
  remaining: number
}

// API接口
export const activityApi = {
  // 获取活动列表
  getList: () => {
    return axios.get<{ data: Activity[] }>(`${API_BASE}/api/activity/list`)
      .then(res => res.data)
  },

  // 获取活动详情
  getDetail: (id: number) => {
    return axios.get<Activity>(`${API_BASE}/api/activity/${id}`)
      .then(res => res.data)
  },

  // 查询抽奖次数
  getDrawCount: (id: number, userId: number) => {
    return axios.get<DrawCountResponse>(`${API_BASE}/api/activity/${id}/draw-count`, {
      params: { user_id: userId }
    }).then(res => res.data)
  },

  // 执行抽奖
  draw: (id: number, userId: number) => {
    return axios.post<DrawResponse>(`${API_BASE}/api/activity/${id}/draw`, {
      user_id: userId
    }).then(res => res.data)
  },

  // 发放抽奖次数
  grant: (id: number, userId: number, count: number = 1) => {
    return axios.post<GrantResponse>(`${API_BASE}/api/activity/${id}/grant`, {
      user_id: userId,
      count
    }).then(res => res.data)
  }
}