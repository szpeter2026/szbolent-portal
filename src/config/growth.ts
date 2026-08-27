/**
 * 增长型门户 · 外链配置（PlanetX 主路径 / DemoPPI 增强层）
 * 称谓与纪律见 docs/GROWTH_PORTAL_NAMING.md
 */

function trimSlash(url: string): string {
  return url.replace(/\/$/, '')
}

/** PlanetX 公网入口（裂变落点） */
export const PLANETX_BASE = trimSlash(
  (import.meta.env.VITE_PLANETX_URL as string | undefined)?.trim() || 'https://app.genz.ltd',
)

/**
 * 运营/博主 referral code（可选）
 * - 空：主 CTA 只链 PlanetX 落点（正式推荐，因落地端尚未消费 ?ref=）
 * - 非空：仅用于验收拼 URL 或内部点检；勿当作生产归因
 * 见 docs/GROWTH_PORTAL_NAMING.md §8.2
 */
export const PLANETX_REF_CODE = (
  (import.meta.env.VITE_PLANETX_REF_CODE as string | undefined) || ''
).trim()

/** DemoPPI 增强层入口；空则前台不展示次 CTA */
export const DEMOPPI_URL = trimSlash(
  (import.meta.env.VITE_DEMOPPI_URL as string | undefined)?.trim() || '',
)

/** 标准邀请 URL：https://app.genz.ltd/?ref={code} */
export function planetxInviteUrl(refCode = PLANETX_REF_CODE): string {
  if (!refCode) return PLANETX_BASE
  const url = new URL(PLANETX_BASE.includes('://') ? PLANETX_BASE : `https://${PLANETX_BASE}`)
  url.searchParams.set('ref', refCode)
  return url.toString()
}

export const growthCtaCopy = {
  title: '从内容走向共识增长',
  lead: '主路径进入 PlanetX；需要先理解「共识」时，可选用 DemoPPI 增强层。裂变邀请在 PlanetX 站内完成。',
  primary: '进入 PlanetX',
  secondary: '先体验共识 Demo（增强层）',
  contact: '联系我们',
} as const
