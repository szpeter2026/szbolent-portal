# Composable 门户 · 学习清单

> **状态：** 学习参考（不照搬）  
> **整理日期：** 2026-08-11  
> **相关：** [`ARCHITECTURE_DECISION_MEMO.md`](./ARCHITECTURE_DECISION_MEMO.md) · [`OPERATIONS_MANUAL.md`](./OPERATIONS_MANUAL.md) · [`DUAL_REPO_WORK_GUIDE.md`](./DUAL_REPO_WORK_GUIDE.md)

---

## 1. 我们在学什么形态

本仓运行时是 **Composable / Headless 门户壳**：

```
浏览器 → Portal (Vite / 静态)
           ├─ /v1/menus|pages|permissions → Page Engine   导航 / 动态页 / 权限
           ├─ /v1/*（其余）                 → Looma         诗词 / RAG / 认证 / 支付
           └─ /wp-json/*                   → WordPress     博客（可选）
```

行业近亲名词：**MACH / Composable Commerce / Headless CMS + SPA + 薄入口（或 BFF）**。  
原则相同：**前端不持有业务真相源；按领域拆后端；入口层管路由/横切，不堆业务。**

---

## 2. 架构好处（为何值得保持）

| 好处 | 对本门户的含义 |
|------|----------------|
| 领域不打架 | 菜单编排 / 诗词智能 / 博客编辑可独立演进 |
| 前端不被主题绑架 | 诗词交互、动效、SPA 路由不受 WP 主题约束 |
| 故障可隔离 | WP 挂了诗词仍可降级服务；Looma 挂了官网壳仍可展示 |
| 技术异构 | Python / Rust / PHP 各干擅长的事 |
| 多端复用 | 同一 Looma API 可服务门户、小程序、海外站 |

---

## 3. 运维难处（必须正视）

| 难点 | 表现 | 缓解方向（已写入架构备忘） |
|------|------|----------------------------|
| 启动矩阵 | 本地需 Portal + Looma + Page Engine + WP(+DB) | Compose 一键启停 + 四端口冒烟 |
| 入口契约漂移 | Vite 代理 ≠ 生产 Nginx 分流 | 契约化 Nginx，与 `vite.config.ts` 对齐 |
| 横切重复 | JWT / CORS / health / 超时各写一套 | 统一健康检查与冒烟清单 |
| 联调面大 | 改字段可能跨两仓三服务 | 双仓工作指引 + API 契约 |
| 真相源漂移 | 易在 WP 再装第二套诗词系统 | WP 仅博客；禁止业务下沉 WP |
| 观测不足 | 排障靠「哪个端口没起来」 | 各服务 `/health` + 部署后冒烟 |

**启发式（不变）：** 服务少、团队小 → **Nginx 薄入口即可**；多端字段差异大或单页需并行拼 3+ 后端时，再评估轻量 BFF / 网关。当前处于临界线偏「薄入口」一侧，**不优先上 Kong / 重 BFF**。

---

## 4. 学习清单（按相似度）

### 4.1 最近似：多后端 + 薄 BFF / 网关

| # | 参考 | 形态 | 建议吸收 |
|---|------|------|----------|
| 1 | [Showpo + KrakenD](https://www.krakend.io/case-study/showpo/) | Shopify + Sanity + 前端，经网关聚合成单一 API | 前端不直连 N 服务；缓存/聚合放入口层 |
| 2 | [Interflora / Kaliop（MACH）](https://www.kaliop.com/en/case-studies/interflora-web) | Nuxt/Vue + GraphQL BFF + CMS + 电商引擎 | Vue 门户 + BFF 拼异构后端；渐进替换遗留 |
| 3 | [Osadkowski composable B2B](https://www.openselfservice.com/blog/osadkowski-composable-b2b-portal-self-service-commerce-lessons-learned) | Next + BFF + 多微服务 / ERP | BFF 先做聚合，再演进领域模型；别一步做重 |
| 4 | [Netguru：Headless Commerce 的 BFF](https://www.netguru.com/blog/backend-for-frontend-for-headless-commerce) | 模式文 | 何时该上 BFF；网关与 BFF 分工 |

### 4.2 更轻、好照着搭：Headless CMS + 独立前端

| # | 参考 | 形态 | 建议吸收 |
|---|------|------|----------|
| 5 | [Headless WP + Next 分机部署](https://www.dchost.com/blog/en/headless-wordpress-next-js-hosting-architecture-for-separate-frontend-and-api-servers/) | FE 与 WP API 分服务器 + 反代 | 与「Portal / WP」拆分最接近的运维拆法 |
| 6 | [Docker 版 Headless WP](https://attowp.com/cms-platforms/headless-cms/headless-wordpress-docker-setup/) | Compose：nginx + WP + FE | 本地一键起停（对齐架构备忘 D6） |
| 7 | [StackBriefly 实践](https://dev.to/jaroslav_svetlik_037b0c11/how-we-built-stackbriefly-as-a-headless-wordpress-and-nextjs-publication-4he1) | WP 编辑 + Next 公网站 | CMS 子域、原子发布、备份体积控制 |
| 8 | [Sanity + Shopify + Next 一年复盘](https://www.imaginaire.co.uk/blog/one-year-with-sanity-building-better-workflows-for-headless-shopify/) | CMS ≠ 业务平台 | 严格分真相源（对齐「WP 不做诗词」） |

### 4.3 模式级 / 已在备忘中的对标

| # | 参考 | 建议吸收 |
|---|------|----------|
| 9 | [Microsoft BFF 模式](https://learn.microsoft.com/en-us/azure/architecture/patterns/backends-for-frontends) | 2～3 服务可薄入口；多端再拆专用 BFF |
| 10 | [好大夫 + Kong](https://developer.cloud.tencent.com/news/731235) | 网关管横切；业务逻辑不下沉网关 |
| 11 | [Optimizely + Next SaaS FE](https://www.matthewdamon.com/building-a-modern-ecommerce-platform-with-optimizely-paas-and-next-js-saas-frontend-part-1-architecture-overview/) | 前后端独立 CI/CD 与观测 |

---

## 5. 建议吸收顺序（对本仓）

1. **Compose + 反代契约**（清单 #5、#6）  
   → 先解决「本地难起、生产分流不一致」。
2. **入口层演进路径**（清单 #1、#3、#4）  
   → 弄清何时从 Nginx 路径分流升级到轻量聚合；未触触发条件不做。
3. **真相源纪律**（清单 #8 + 本仓备忘 D2/D3）  
   → WP 只做博客；Page Engine 做管理中枢；Looma 做智能与认证。
4. **刻意不学**  
   → 一上来几十个微服务 + 重 BFF。规模未到时，运维成本会先吃掉交付节奏。

---

## 6. 与本仓决策的对照（一句话）

| 别人常做的 | 我们当前选择 |
|------------|--------------|
| 完整 API Gateway / 重 BFF | Nginx（生产）+ Vite proxy（开发）薄入口 |
| CMS 兼做业务后台 | WP 可选且收窄；中枢 = Page Engine |
| 单体主题站 | Vue 门户壳 + 多领域 API |
| 本地手工起 N 个进程 | **目标：** Compose + 四端口冒烟（尚未完全产品化） |

**结论：** 架构方向与 MACH / composable 同族；当前最大短板不是「不够拆」，而是 **入口契约 + 一键启停 + 冒烟** 尚未产品化。

---

## 7. 阅读时注意

- 案例多为电商 / B2B，业务域不同，只借 **边界与运维模式**，不借 SKU / 购物车实现。  
- 链接可能变更；以原文为准，本页只作索引。  
- 变更本仓架构决策时，优先改 [`ARCHITECTURE_DECISION_MEMO.md`](./ARCHITECTURE_DECISION_MEMO.md)，再视需要更新本清单。

---

*收敛自 2026-08-11 架构讨论；学习用，不构成实施承诺。*
