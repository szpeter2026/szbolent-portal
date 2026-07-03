/**
 * 活动抽奖API接口
 */
import axios from 'axios'

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8001'

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
