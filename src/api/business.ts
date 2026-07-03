/**
 * 后端业务 API
 * 服务、案例、职位、联系等
 */

const API_BASE = '/api'

// ============ 服务相关 ============

export interface Service {
  id: number
  slug: string
  title: string
  icon?: string
  description: string
  overview?: string
  features: string[]
  benefits: string[]
  sort_order: number
  is_active: boolean
  created_at?: string
  updated_at?: string
}

/**
 * 获取服务列表
 */
export async function getServices(): Promise<Service[]> {
  try {
    const response = await fetch(`${API_BASE}/services`)
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '获取服务列表失败')
    }
    
    return result.data
  } catch (error) {
    console.error('获取服务列表失败:', error)
    throw error
  }
}

/**
 * 获取服务详情
 */
export async function getServiceBySlug(slug: string): Promise<Service> {
  try {
    const response = await fetch(`${API_BASE}/services/${slug}`)
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '获取服务详情失败')
    }
    
    return result.data
  } catch (error) {
    console.error('获取服务详情失败:', error)
    throw error
  }
}

// ============ 案例相关 ============

export interface CaseStudy {
  id: number
  slug: string
  title: string
  subtitle?: string
  industry: string
  industry_id: string
  service: string
  image_url?: string
  description: string
  overview?: string
  features: string[]
  benefits: string[]
  stats: Array<{ label: string; value: string }>
  is_featured: boolean
  is_active: boolean
  created_at?: string
  updated_at?: string
}

/**
 * 获取案例列表
 */
export async function getCases(industry?: string): Promise<CaseStudy[]> {
  try {
    const params = new URLSearchParams()
    if (industry) params.append('industry', industry)
    
    const response = await fetch(`${API_BASE}/cases?${params.toString()}`)
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '获取案例列表失败')
    }
    
    return result.data
  } catch (error) {
    console.error('获取案例列表失败:', error)
    throw error
  }
}

/**
 * 获取案例详情
 */
export async function getCaseBySlug(slug: string): Promise<CaseStudy> {
  try {
    const response = await fetch(`${API_BASE}/cases/${slug}`)
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '获取案例详情失败')
    }
    
    return result.data
  } catch (error) {
    console.error('获取案例详情失败:', error)
    throw error
  }
}

// ============ 职位相关 ============

export interface Job {
  id: number
  title: string
  department: string
  department_id: string
  location: string
  location_id: string
  job_type: string
  type_id: string
  salary?: string
  description: string
  requirements: string[]
  responsibilities?: string[]
  benefits?: string[]
  publish_date: string
  is_active: boolean
  created_at?: string
  updated_at?: string
}

export interface JobApplication {
  name: string
  email: string
  phone: string
  resume_url?: string
  intro: string
}

/**
 * 获取职位列表
 */
export async function getJobs(filters?: {
  department?: string
  location?: string
  type?: string
}): Promise<Job[]> {
  try {
    const params = new URLSearchParams()
    if (filters?.department) params.append('department', filters.department)
    if (filters?.location) params.append('location', filters.location)
    if (filters?.type) params.append('type', filters.type)
    
    const response = await fetch(`${API_BASE}/jobs?${params.toString()}`)
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '获取职位列表失败')
    }
    
    return result.data
  } catch (error) {
    console.error('获取职位列表失败:', error)
    throw error
  }
}

/**
 * 获取职位详情
 */
export async function getJobById(id: number): Promise<Job> {
  try {
    const response = await fetch(`${API_BASE}/jobs/${id}`)
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '获取职位详情失败')
    }
    
    return result.data
  } catch (error) {
    console.error('获取职位详情失败:', error)
    throw error
  }
}

/**
 * 申请职位
 */
export async function applyJob(jobId: number, application: JobApplication): Promise<void> {
  try {
    const response = await fetch(`${API_BASE}/jobs/${jobId}/apply`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(application),
    })
    
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '申请提交失败')
    }
  } catch (error) {
    console.error('申请提交失败:', error)
    throw error
  }
}

// ============ 联系表单 ============

export interface ContactForm {
  name: string
  email: string
  phone?: string
  company?: string
  subject?: string
  message: string
}

/**
 * 提交联系表单
 */
export async function submitContact(contact: ContactForm): Promise<void> {
  try {
    const response = await fetch(`${API_BASE}/contacts`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(contact),
    })
    
    const result = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || '提交失败')
    }
  } catch (error) {
    console.error('提交联系表单失败:', error)
    throw error
  }
}

// ============ 工具函数 ============

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
 * 格式化日期时间
 */
export function formatDateTime(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleString('zh-CN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}
