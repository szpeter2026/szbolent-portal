# 执行方向共识

> **真源文档：** [`PlanetX/docs/EXECUTION_DIRECTION.md`](../../PlanetX/docs/EXECUTION_DIRECTION.md)  
> **M0 完成：** 2026-07-17  
> **Plan A 收口：** 2026-07-19  
> **Plan B 收口：** 2026-07-20

---

## Plan A · Page Engine 路由闭环 ✅

**目标：** Page Engine 从「喂导航」升级到「管路由」  
**时间：** 2026-07-19 收口

| 验收项 | 状态 |
|--------|------|
| 动态菜单 Header/Footer（Page Engine :5300） | ✅ |
| DB 驱动路由（blog/careers） | ✅ 全刷 + SPA |
| `/cases` → `/case-study` 重定向 | ✅ |
| `await menusInit` 再 `app.use(router)` | ✅ |

**关键修正：** 未登录 `/auth/profile` 401 属预期行为，不阻塞主流程。

---

## Plan B · 诗词 AI 对话全链路 ✅

**目标：** 诗词页 ChatDialog → Looma RAG 问答  
**时间：** 2026-07-20 收口

| 验收项 | 状态 |
|--------|------|
| ChatDialog 组件（状态机：登录→授权→对话） | ✅ |
| Looma ask API 联调 | ✅ |
| consent 授权流程 | ✅ |
| login→ask_rag 授权→提问→AI 回答（全链路 Playwright 验证） | ✅ |

### 关键修正（3 处 bug）

| 文件 | 问题 | 修正 |
|------|------|------|
| `looma.ts` `grantConsent()` | 路径 `/consent/grant`（404） | → `/compliance/consent/grant` |
| `looma.ts` `login()` | 取 `data.token`，实际响应字段 `access_token` | → `data.access_token \|\| data.token` |
| `looma.ts` `ask()` | 发 `question` 字段，实际 API 字段 `query` | → `query`；去掉不存在的 `session_id` |

### ChatDialog 状态机

```
COLLAPSED (浮动按钮)
  → 未认证 → LOGIN (调用 login() API)
    → 未授权 → CONSENT (调用 grantConsent('ask_rag'))
      → CHAT (调用 ask(query) → 展示 answer + intent + sources)
```

- **自愈设计：** `ask()` 被后端拒时，自动 `grantConsent()` → 重试问卷；失败回 consent 屏
- **401 踢回：** token 过期自动清 localStorage + 回登录屏

---

## M0 完成（不变）

| 任务 | 产物 |
|------|------|
| JobFirst 收件箱闭环 | `PlanetX/scripts/demo-jobfirst-inbox.sh` |
| 事件契约 | `PlanetX/docs/EVENT_SCHEMA.md` |
| 海外 PWA（代码） | `PlanetX/public/manifest.json`、`sw.js`、`vercel.json`、图标 |

## 待手动（海外上线）

1. Vercel 项目 → Root `PlanetX` → 输出 `.`
2. DNS `app.genz.ltd` → Vercel
3. `deploy-overseas.yml` CORS 加 `https://app.genz.ltd`

---

## 当前架构总览

> **决策备忘（边界 / 迭代 / 对标）：** [`ARCHITECTURE_DECISION_MEMO.md`](./ARCHITECTURE_DECISION_MEMO.md)  
> **建站定位（文化智能 / 行业信任 / 双线）：** [`SITE_POSITIONING_MEMO.md`](./SITE_POSITIONING_MEMO.md)  
> **增长称谓（Content + PLG）：** [`GROWTH_PORTAL_NAMING.md`](./GROWTH_PORTAL_NAMING.md)  
> **学习清单（对标案例）：** [`COMPOSABLE_PORTAL_LEARNING.md`](./COMPOSABLE_PORTAL_LEARNING.md)

```
szbolent-portal (Vite :3000)
  ├── /v1/* → Looma :5200        ─ 诗词/RAG/认证
  ├── /v1/* → Page Engine :5300  ─ 菜单/路由/动态页面
  └── /wp-json/* → WordPress     ─ 博客 CMS 内容
```

| 层 | 端口 | 职责 | 状态 |
|----|------|------|------|
| Page Engine | :5300 | 菜单/路由/动态内容 | Plan A 收口 |
| Looma | :5200 | 诗词/RAG/认证 | Plan B 收口 |
| Portal (Vite) | :3000 | 消费端，proxy 串联 | 持续迭代 |

---

## 冻结项（不变）

- Looma 全链路匹配
- 微信小程序提审
- RingGuard B2B 主动拓展
- 宏伟叙事扩 scope

---

## 下一轮

- 诗人直链修复（穿插小修）
- PWA 配置补完（穿插小修）
- YeDall anchor 接入 · 录屏 · 3–5 人封闭试用

完整细节见 PlanetX 真源文档 §9。
