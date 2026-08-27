# 双仓同步状态

> 最后更新：2026-07-20（Plan A + Plan B 收口）

## 架构概览

```
szbolent-portal (Vite :3000)          Looma (:5200) / Page Engine (:5300)
┌─────────────────────────────┐       ┌──────────────────────────────┐
│  src/                       │       │  Looma (Axum Rust)           │
│  ├─ api/looma.ts           │──◄───│  ├─ /v1/poetry/*             │
│  ├─ views/poetry/*         │       │  ├─ /v1/ask (RAG)            │
│  ├─ components/shared/     │       │  ├─ /v1/auth/*              │
│  │   ChatDialog.vue         │       │  └─ /v1/compliance/consent/* │
│  ├─ router/index.ts        │──◄───│  Page Engine (Salvo Rust)    │
│  ├─ main.ts                │       │  ├─ /v1/menus               │
│  └─ composables/           │       │  ├─ /v1/products            │
│      usePermission.ts      │       │  └─ /v1/pages/*             │
└─────────────────────────────┘       └──────────────────────────────┘
```

## 两仓同步状态

| 功能 | 门户仓 (szbolent-portal) | 服务仓 (Looma/Page Engine) | 状态 |
|------|------------------------|---------------------------|------|
| 诗词列表/详情 | `src/views/poetry/` | Looma :5200 /v1/poetry/* | ✅ |
| 诗词 AI 对话 | `src/components/shared/ChatDialog.vue` | Looma :5200 /v1/ask + /v1/compliance/consent/grant | ✅ |
| 用户认证 | `src/composables/usePermission.ts` + `looma.ts` login | Looma :5200 /v1/auth/login + /v1/auth/profile | ✅ |
| 同意授权 | `looma.ts` grantConsent | Looma :5200 /v1/compliance/consent/grant | ✅ |
| 动态菜单/路由 | `src/main.ts` + `src/router/index.ts` | Page Engine :5300 /v1/menus | ✅ |
| 动态页面 | `src/router/index.ts` 动态注册 | Page Engine :5300 /v1/pages/* | ✅ |
| Header/Footer | `src/components/Header.vue` / `Footer.vue` | Page Engine :5300 /v1/menus + /v1/products | ✅ |
| 博客 | `src/views/Blog.vue` 等 | WordPress :8800 /wp-json/*（本地） | ✅ |
| `/cases` → `/case-study` | router 重定向 | - | ✅ |
| 翻译 i18n | `src/i18n/` | - | ⚡ 持续补充 |
| About / Contact | `src/views/Home.vue` / `Contact.vue` | Page Engine :5300 | ⚡ 待确认 |
| 案例详情 pages | 待对接 | Page Engine :5300 | ⚡ 待对接 |

### 状态说明

- ✅ 双仓就绪 / 联调通过
- ⚡ 门户仓就绪 / 服务 API 可用或进行中
- 🔄 进行中
- ⏸ 阻塞
- ❌ 未启动

## API 契约

详见 [`../api-contract.yaml`](../api-contract.yaml) 和 [`INTEGRATION.md`](./INTEGRATION.md)。

### ask API 字段约定

- **请求字段：** `query`（非 `question`）
- **登录响应：** `access_token`（非 `token`）
- **consent 路径：** `/v1/compliance/consent/grant`（非 `/v1/consent/grant`）

## 联调里程碑

| 日期 | 里程碑 | 验收 |
|------|--------|------|
| 2026-07-17 | M0 完成 | JobFirst 收件箱 + 事件契约 + PWA 代码 |
| 2026-07-19 | Plan A 收口 | Page Engine 管路由，动态菜单/路由闭环 |
| **2026-07-20** | **Plan B 收口** | **ChatDialog 全链路：login → consent → ask → answer** |

## 下一步待办

| # | 任务 | 优先级 | 说明 |
|---|------|--------|------|
| 1 | 诗人直链修复 | 穿插小修 | 不挡主路径 |
| 2 | PWA 配置补完 | 穿插小修 | vite-plugin-pwa 接入 |
| 3 | YeDall 锚定集成 | 下一主线 | DID + VC + 锚定 |
| 4 | 第一轮录屏/演示 | 按需 | 完整 walkthrough |
| 5 | 3-5 人封闭试用 | 按需 | 反馈收集 |
