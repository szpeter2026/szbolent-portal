# 集成清单

> **腾讯云备案 / 支付 / 双仓联调：** 见 **[TENCENT_CLOUD_COMMERCE.md](./TENCENT_CLOUD_COMMERCE.md)**（与 `looma-zervi` 同文同步）

## 架构概览

```
szbolent-portal (Vue3 + Vite, 纯前端 SPA)
    ├── HTTP → Looma API (:5200)
    │   ├── /v1/poetry/*      ← 诗词浏览、搜索（SQLite + ChromaDB）
    │   ├── /v1/ask           ← 通用 RAG 问答（已融合原 Tatha RAG）
    │   ├── /v1/auth/*        ← JWT 认证
    │   └── /v1/payment/*     ← 支付（P1）
    └── HTTP → WordPress REST API (:8800)
        └── /wp-json/wp/v2/*  ← 博客内容
```

**后端只需部署两个服务：Looma API + WordPress。** 原 Tatha RAG 已完全融入 Looma（`backend/src/rag/`），不再需要独立部署。

## 1. 门户前台（本仓）

```bash
cd /Users/jason/Projects/szbolent-portal
cp .env.example .env.local
npm install
npm run dev
```

默认：**http://localhost:3000**

## 2. Looma API（诗词 + RAG + 用户 + 支付）

**源码：** `/Users/jason/Projects/looma-zervi/backend`

```bash
cd /Users/jason/Projects/looma-zervi/backend
source venv/bin/activate
python run.py   # 确认 PORT=5200
```

门户通过 `src/api/poetry.ts` 调用 `/v1/poetry/*`。  
开发代理见 `vite.config.ts` → `/v1`。

**诗词联调（Looma 字段契约）：**

| 门户方法 | Looma 路由 | 主要参数 / 响应 |
|----------|-----------|----------------|
| `getPoems` | `GET /v1/poetry/browse` | `dynasty`, `author`, `theme`, `keyword`, `page`, `per_page` → `{ items, total, page, per_page }`；item 含 `content_preview` |
| `getPoem` | `GET /v1/poetry/:id` | 全文 `content` |
| `getRandom` | `GET /v1/poetry/random` | `count`（非 `n`）→ `{ results, count }` |
| `search` | `GET /v1/poetry/search` | `q`, `n` → ChromaDB 语义搜索 |
| `getStats` | `GET /v1/poetry/stats` | `total`, `dynasties`, `themes` |

**RAG 能力（已集成）：**

| 能力 | Looma 位置 | 说明 |
|------|-----------|------|
| 通用 RAG 问答 | `backend/src/rag/chroma_client.py` → `/v1/ask` | ChromaDB `looma_knowledge` 集合 |
| 诗词语义搜索 | `backend/src/agents/poetry_search.py` → ChromaDB `poetry_full` | 嵌入式 PersistentClient |
| 嵌入生成 | `backend/src/rag/embeddings.py` | 支持 Ollama / DeepSeek / OpenAI |

## 3. WordPress Headless（Poetry-modown）

**源码镜像：** `SurfaceZervi/GitHub/szjason72/Poetry-modown`

本地可选：

```bash
docker compose -f docker-compose.wp.yml up -d
```

门户通过 `src/api/wordpress.ts` 读 `/wp-json/wp/v2/posts`。  
开发代理见 `vite.config.ts` → `/wp-json`。

## 4. 生产部署（www.szbolent.com.cn）

| 组件 | 建议 |
|------|------|
| 门户静态站 | `npm run build` → CDN / Nginx |
| Looma API | 同域反代 `/v1` 或独立 `api.szbolent.com.cn` |
| WordPress | 同域反代 `/wp-json` 或子域 `cms.szbolent.com.cn` |

**生产环境变量（`.env.production`）：**

```env
VITE_SITE_URL=https://www.szbolent.com.cn
VITE_LOOMA_API_BASE=https://api.szbolent.com.cn
VITE_BLOG_API_BASE=https://cms.szbolent.com.cn/wp-json/wp/v2
```

## 5. 第一周 PR 粒度

1. 品牌：`company.ts` + `index.html` + Home Hero ✅（基线已做）
2. WP 联调：Blog 列表读真实 posts
3. 诗词页：接 Looma `/v1/poetry/*`
4. 部署文档 + DNS

## 6. 历史说明

- **Tatha RAG（旧 `poetry_rag`）** 已完全融入 Looma，位于 `backend/src/rag/`，不再作为独立服务存在。
- **Legacy bolent Sanic `:8001`** 已由 Looma API 替代，`src/api/poetry.ts` 对接 Looma `/v1/poetry/*`。
- 门户 `src/api/tatha.ts` 保留但默认关闭（`VITE_TATHA_POETRY_ENABLED=false`），指向旧 Tatha 独立服务，不应在生产环境启用。
