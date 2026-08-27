# 架构决策备忘 · Composable 门户栈

> **状态：** 已共识（2026-07-21）  
> **范围：** szbolent-portal 运行时形态与下一阶段迭代边界  
> **相关：** [`SITE_POSITIONING_MEMO.md`](./SITE_POSITIONING_MEMO.md) · [`EXECUTION_DIRECTION.md`](./EXECUTION_DIRECTION.md) · [`INTEGRATION.md`](./INTEGRATION.md) · [`OPERATIONS_MANUAL.md`](./OPERATIONS_MANUAL.md)

---

## 1. 当前形态（一句话）

**Composable / Headless 组合栈**：Vue 门户 + 多领域后端，开发期用 Vite 代理、生产期用 Nginx 路径路由做薄入口。不是单体，也尚未上完整微服务网关。

```
Portal (Vite / 静态)
  ├── /v1/menus|pages|permissions → Page Engine :5300   ← 菜单 / 动态路由 / Schema 页
  ├── /v1/*（其余）               → Looma :5200         ← 诗词 / RAG / 认证 / 支付
  └── /wp-json/*                  → WordPress           ← 博客 CMS（可选）
```

| 层 | 真相源职责 | 非职责 |
|----|------------|--------|
| **Page Engine** | 菜单、路由、动态页面、权限 | 诗词数据、RAG、支付 |
| **Looma** | 诗词全库、AI 问答、JWT/consent、支付 | 门户菜单、博客正文 |
| **WordPress** | 博客 posts / categories / tags | 诗词管理、产品管理后台、门户壳 |
| **Portal** | 体验与集成消费 | 不自建第二套内容真相源 |

---

## 2. 决策记录

| # | 决策 | 理由 |
|---|------|------|
| D1 | **维持前后端分离 + 多服务拆分** | Plan A/B 已验证；领域边界清晰，可独立演进 |
| D2 | **WP 仅作可选博客 CMS，不扩展业务** | 「诗词宝库」插件与门户重复且绑死废弃 `:8001`；价值低，不安装 |
| D3 | **管理中枢方向 = Page Engine，不是 WordPress** | 菜单/页面/权限已在同一服务；避免第三套管理后台 |
| D4 | **生产路径分流必须与 Vite 代理契约一致** | 云上若仍整段 `/v1` 回源错误后端，仿真必挂 |
| D5 | **暂缓 Kong / KrakenD 等完整 BFF 网关** | 当前 2～3 后端、小团队；Nginx 路径路由足够。多端聚合或强缓存/限流再评估 |
| D6 | **本地目标：Compose 一键起停 + 四端口冒烟** | 降低「多终端手工启停」运维摩擦 |

---

## 3. 优缺点（相对本栈）

**优点**

- 领域清晰：内容编排 / 智能数据 / 博客各有真相源（对齐行业 composable 做法）
- 前端体验不受 WP 主题绑定；故障可按服务隔离
- 技术栈可异构（Rust / Python / PHP）

**缺点**

- 运维面随服务数上升：启动顺序、JWT 对齐、CORS、健康检查
- 无统一入口契约时，前端/代理易成隐式集成层
- 多 CMS 易重复建设（已用诗词插件踩坑）
- 多仓多流水线，联调与发布矩阵偏重

---

## 4. 外部对标（学习用，不照搬）

> **完整学习清单（好处 / 运维难处 / 案例索引 / 吸收顺序）：**  
> [`COMPOSABLE_PORTAL_LEARNING.md`](./COMPOSABLE_PORTAL_LEARNING.md)

| 参考 | 形态 | 可吸收 |
|------|------|--------|
| [Showpo + KrakenD](https://www.krakend.io/case-study/showpo/) | Headless 店面 + 多后端 + BFF | 正式入口做聚合/缓存；前端不直连 N 服务 |
| [Sanity + Shopify + Next](https://www.imaginaire.co.uk/blog/one-year-with-sanity-building-better-workflows-for-headless-shopify/) | CMS ≠ 业务平台 | 严格分真相源；CMS 存引用，运行时拼装 |
| [Optimizely + Next SaaS FE](https://www.matthewdamon.com/building-a-modern-ecommerce-platform-with-optimizely-paas-and-next-js-saas-frontend-part-1-architecture-overview/) | Headless + 独立前端部署 | 前后端独立 CI/CD 与观测 |
| [好大夫 + Kong](https://developer.cloud.tencent.com/news/731235) | 接入网关 + BFF + 多语言服务 | 网关管横切；业务逻辑不下沉网关 |
| [Microsoft BFF](https://learn.microsoft.com/en-us/azure/architecture/patterns/backends-for-frontends) | 官方模式 | 2～3 服务可薄入口；多端再拆专用 BFF |

业界启发式：**服务少、团队小 → 薄网关即可；服务多或要页面级聚合 → 再上 BFF。** 我们处在临界线偏「薄入口」一侧。

---

## 5. 下一阶段迭代（有序）

1. **契约化 Nginx**：把 `vite.config.ts` 分流规则写成生产配置（与 D4 一致），再谈云上全量仿真。  
2. **本地 Compose + 冒烟**：WP → Looma → Page Engine → Portal；`menus` / `poetry/stats` / `wp posts` 一键验。  
3. **CMS 收敛评估（中期二选一）**  
   - 博客量小 → 收进 Page Engine（少一个运行时）  
   - 编辑强依赖 WP → 保留 headless，但禁止第二套页面/诗词系统  
4. **最小可观测**：各服务 `/health` + 部署后冒烟清单（写入运维手册）。  
5. **触发 BFF 升级的条件（未满足则不做）**：小程序+Web 双端字段差异大、单页需并行拼 3+ 后端、或边缘缓存/限流成为刚需。

---

## 6. 明确不做

- 不为博客短代码去适配「诗词宝库」整插件  
- 不把诗词 / 认证 / 支付做进 WordPress  
- 不在主环路未云上仿真前，再拆更多微服务  
- 不在 Stripe/实体审查冻结期改动海外营销站（genz.ltd Vercel）发布节奏（另线决策）

---

## 7. 验收口径

| 项 | 通过标准 |
|----|----------|
| 边界 | 任意新功能能归入上表「真相源」之一，且不双写 |
| 入口 | 生产 `/v1` 分流与本地 Vite 一致，冒烟 200 |
| 运维 | 文档化一键启停；关 IDE 不再是唯一停服方式 |
| 管理 | 产品管理面不以 WP 为中枢 |

---

*备忘收敛自 2026-07-21 架构调研与团队共识；变更时更新日期与决策表行号。*
