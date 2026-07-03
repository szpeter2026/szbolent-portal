/**
 * Tatha poetry_rag 只读接口（可选）
 * 合同见 SurfaceZervi/lineage/NARRATIVES/bolent-content.md
 */
import axios from 'axios'

const TATHA_BASE = import.meta.env.VITE_TATHA_URL || 'http://127.0.0.1:8010'
const ENABLED = import.meta.env.VITE_TATHA_POETRY_ENABLED === 'true'

export interface PoetryRagResult {
  answer: string
  sources?: Array<{ title?: string; poet_name?: string }>
}

export async function queryPoetryRag(question: string): Promise<PoetryRagResult | null> {
  if (!ENABLED) return null
  try {
    const { data } = await axios.post(`${TATHA_BASE}/v1/rag/query`, {
      query: question,
      namespace: 'poetry_rag',
    })
    return data
  } catch (err) {
    console.warn('[tatha] poetry_rag unavailable', err)
    return null
  }
}
