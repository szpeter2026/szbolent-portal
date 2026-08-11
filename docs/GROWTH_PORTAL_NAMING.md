# 增长型门户 · 团队统一称谓备忘

> **状态：** 团队共识草案（2026-08-11）  
> **用途：** 统一营销与产品对外/对内称谓，避免「官网 / DXP / 自媒体 / DemoPPI 主链」混称  
> **相关：** [`SITE_POSITIONING_MEMO.md`](./SITE_POSITIONING_MEMO.md) · [`COMPOSABLE_PORTAL_LEARNING.md`](./COMPOSABLE_PORTAL_LEARNING.md) · [`ARCHITECTURE_DECISION_MEMO.md`](./ARCHITECTURE_DECISION_MEMO.md)

---

## 1. 标准总称（请统一使用）

**中文标准称谓：**

> **Bolent 增长型门户体系**  
> （内容获客 + 产品内裂变；DemoPPI 为增强层）

**英文标准称谓（对内文档 / 对外简述可用）：**

> **Bolent Growth Portal**  
> *Content-led Acquisition + In-product Viral Loop*  
> （亦可简写：**Content + PLG Funnel**）

**一句话定义：**

> 以门户博客为内容入口，以 PlanetX 邀请码为裂变主路径，以 DemoPPI 为共识理解增强层的增长体系。

---

## 2. 三层标准名（勿混用）

| 层级 | 标准中文名 | 标准英文名 | 真源 / 仓库 | 营销职责 |
|------|------------|------------|-------------|----------|
| **入口层** | 门户内容入口 | Content Hub / Brand Portal | `szbolent-portal`（博客等） | 不定期内容更新，吸引关注与话题 |
| **主链层** | PlanetX 裂变主路径 | PLG Viral Loop | `looma-zervi` → `frontend/packages/planetx` + `/v1/referral` | 邀请码（`?ref=`）传播与注册转化 |
| **增强层** | DemoPPI 共识增强层 | Activation / Understanding Layer | `DemoPPI`（独立域，勿占 bolent 主域） | 加深共识理解与关系体验，提高裂变质量 |

**路径示意（唯一标准叙事）：**

```
门户博客（内容触达）
  → PlanetX 邀请码裂变（增长真源）
    → DemoPPI（可选增强：加强了解，不阻塞主链）
```

---

## 3. 称谓对照（推荐 vs 禁止）

| 场景 | ✅ 推荐说法 | ❌ 避免说法 | 原因 |
|------|-------------|-------------|------|
| 整套体系 | Bolent 增长型门户体系 | 「我们是 Liferay / DXP」 | 定位错位，预期过大 |
| 门户站 | 内容入口 / 品牌门户 | 「纯营销落地页」「迷你中台」 | 忽视诗词/AI 与内容资产 |
| 博客 | 内容获客层 | 「自媒体主战场」 | 主传播仍常在社媒；博客是原文与信任托管 |
| PlanetX 邀请 | 裂变主路径 / PLG 邀请 | 「DemoPPI 邀请码」 | 邀请真源在 Looma referral，不在 DemoPPI |
| DemoPPI | 增强层 / 共识体验增强 | 「完整共识主链路」「没有就不能裂变」 | 增强非硬依赖 |
| 合称策略 | Content + PLG 双轮驱动 | 「三个产品各喊各的」 | 策略无法对齐 |

---

## 4. 与建站定位的对齐

见 [`SITE_POSITIONING_MEMO.md`](./SITE_POSITIONING_MEMO.md)：

| 建站叙事 | 在本增长体系中的角色 |
|----------|----------------------|
| 主叙事 · 文化智能 | 内容与体验差异化（含诗词/AI）；博客可承载文化与技术内容 |
| 辅叙事 · 行业信任 | 资质与交付背书，服务 B2B 信任，不替代裂变主路径 |
| 骨架 · 双线 | `.cn` 内容入口 vs 商业/API 域；PlanetX 裂变走产品域，不塞进个人备案支付 |

---

## 5. 对外话术（可直接复用）

**对内（策略会）：**  
我们做的是 **Bolent 增长型门户**：内容入口 + PlanetX 裂变主路径 + DemoPPI 增强层。

**对外（简版）：**  
Bolent 以内容门户触达用户，并通过 PlanetX 邀请机制实现产品内增长；DemoPPI 用于加深共识体验。

**对外（商务版）：**  
我们不是传统企业官网堆砌，而是 **Content + PLG** 结构：内容建立信任，邀请码完成扩散，共识体验提升转化质量。

---

## 6. 决策纪律（策略统一用）

1. **主链唯一：** 裂变与邀请码指标只认 PlanetX / Looma referral。  
2. **增强可选：** DemoPPI 可独立排期；不得写进「上线门槛」。  
3. **域名分置：** DemoPPI 不用 `szbolent.cn` 主品牌域承载。  
4. **内容不双写：** 长文在门户 WP/博客；DemoPPI / PlanetX 不做第二套 CMS。  
5. **称谓冻结：** 对外材料、周报、路演统一用本文 §1–§2 名称；新增别名需改本备忘。

---

## 7. 验收口径（称谓是否用对）

| 检查项 | 通过标准 |
|--------|----------|
| 周报/路演 | 出现「增长型门户」或 Content + PLG，而非「DXP / 纯官网」 |
| 邀请讨论 | 明确指向 PlanetX / referral，不说成 DemoPPI 主邀请 |
| DemoPPI 排期 | 标注为增强层，无「阻塞裂变上线」表述 |
| 门户博客目标 | 写清「内容获客」，不写「替代 PlanetX 增长」 |

---

## 8. 适配清单（团队排期用）

> **标准路径：** 门户博客 → PlanetX 邀请裂变（主）→ DemoPPI（可选增强）  
> **原则：** 先链接、后体验、再身份；不做 SSO；DemoPPI 不阻塞裂变上线。  
> **数据面原则：** 增长主链以**自有云**（Looma / 自建存储与 API）为唯一真源；**非反 Supabase，反重复建设**。

### 8.0 数据面与 Supabase（共识表述）

**写死给团队的一句：**

> **增长型门户以自有云（Looma 等）为唯一数据与裂变真源；Supabase 仅可用于 DemoPPI 等增强层试验，不作为主链重复建设，也不阻塞 PlanetX 邀请上线。**

| 态度 | 含义 |
|------|------|
| ❌ 不是 | 「为反 Supabase 而反」、意识形态弃用 |
| ✅ 而是 | 自有云已向用户提供存储与数据服务；增长主链不必再叠一套 BaaS（多账号、多库、多备份、多费用、多排障面） |
| ✅ 允许 | DemoPPI 短期试验继续用 Supabase，但标注为**增强层**，不进主链 SLA、不占裂变指标真源 |
| ✅ 若增强要产品化 | 优先接到 Looma / 自有存储，而不是把 Supabase 扩成第二正式数据面 |

**与三层的对齐：**

| 层 | 数据面期望 |
|----|------------|
| 门户内容入口 | 自有云 + WP（可选博客）；不引入 Supabase |
| PlanetX 裂变主路径 | Looma（`api.genz.ltd` / `/v1/referral` 等）— **唯一裂变真源** |
| DemoPPI 增强层 | 可用 Supabase 做试验；长期产品化则迁自有云，避免与 Looma 双轨并行服务同一用户群 |

### 8.1 门户已落地（P0 · 本仓）

| 项 | 状态 | 说明 |
|----|------|------|
| 环境变量 | ✅ | `VITE_PLANETX_URL` / `VITE_PLANETX_REF_CODE` / `VITE_DEMOPPI_URL`（见 `.env.example`） |
| 邀请 URL 组装 | ✅ | `src/config/growth.ts` → `planetxInviteUrl()` = `{PLANETX_URL}/?ref={code}`；无 ref 时只回 base |
| 博客详情双 CTA | ✅ | `BlogDetail.vue`：主按钮 PlanetX；次按钮 DemoPPI（仅当 `VITE_DEMOPPI_URL` 非空）；保留「联系我们」 |
| 称谓文档 | ✅ | 本文 §1–§7 |
| **本地验收** | ✅ **2026-08-11** | 见下方验收记录 |

**本地配置示例（`.env.local`）：**

```bash
VITE_PLANETX_URL=https://app.genz.ltd
VITE_PLANETX_REF_CODE=          # 填运营/博主码后主按钮自动带 ?ref=
VITE_DEMOPPI_URL=               # 填独立域后显示次 CTA，例如 https://ppi.example.com
```

改 env 后需重启 `npm run dev`。

**验收（门户 P0）— 已通过（2026-08-11 · `/blog/hello-world`）：**

| 项 | 结果 |
|----|------|
| 文末标题「从内容走向共识增长」 | ✅ |
| 主 CTA → `https://app.genz.ltd/?ref=portal-verify` | ✅ |
| 次 CTA → `https://ppi.example.com`（已填 `VITE_DEMOPPI_URL`） | ✅ |
| 「联系我们」→ `/contact` | ✅ |
| ref hint 文案（有码时显示） | ✅ |
| `growth.ts` 无 ref 时只回 base | ✅（逻辑核对） |
| `VITE_DEMOPPI_URL` 为空时次按钮隐藏 | ✅（`v-if="demoppiUrl"`） |

> **注意：** 验收时 `.env.local` 使用测试值 `VITE_PLANETX_REF_CODE=portal-verify`、`VITE_DEMOPPI_URL=https://ppi.example.com`。  
> **门户 P0 只验收「URL 拼装与 CTA 展示」**，不验收 Looma 核销。  
> **现阶段正式运营默认：主 CTA 不带 ref**（`VITE_PLANETX_REF_CODE` 留空 → 只链 `https://app.genz.ltd`）。原因见 §8.2。

### 8.2 PlanetX 裂变主路径（待适配 · 含阻塞项）

#### 8.2.1 代码现状（2026-08-11 核对）

| 事实 | 证据 | 含义 |
|------|------|------|
| Looma 可发真实码 | `POST /v1/referral/create` → 8 位大写码；`purpose=referral` 每次新建 | ✅ 能拿到真码 |
| Looma `/use` **一次性** | `WHERE code = ? AND used_by IS NULL`，用完即 404 | 固定渠道码不能服务多人 |
| 门户可拼 `?ref=` | `growth.ts` → `planetxInviteUrl()` | ✅ URL 层已就绪 |
| **PlanetX 落地不消费 `?ref=`** | `consumeJoinInviteFromUrl()` 只解析 `?join=`（舰队）；`register()` 无邀请字段；前端 **零调用** `/v1/referral/use`；无 utm 读取 | ⛔ **阻塞**：门户带真码也无归因 |

**结论矩阵：**

| 做法 | 当前状态 | 结论 |
|------|----------|------|
| 门户带固定真实码 | 落地不消费 `?ref=` + 码一次性 | ❌ 双重失效，勿当生产归因 |
| 用户进 PlanetX **站内裂变** | SharePanel + `ensureReferralCode` 已实现 | ✅ **现成可用主路径**（产品设计本意） |
| 门户渠道码 + 多人归因 | 需 PlanetX 实现 §8.2.2 P1-阻塞 + Looma `max_uses`/campaign | ⚠️ 后续专项，两处都改才有意义 |

#### 8.2.2 排期表

| 优先级 | 项 | Owner | 状态 | 验收 |
|--------|----|--------|------|------|
| P0 | 公网落点 `https://app.genz.ltd` | PlanetX / 运维 | 可用 | 外链可开、CORS 对齐 API |
| P0 | 门户主 CTA **默认不带 ref**（只落点） | 门户运营 | **现行推荐** | `VITE_PLANETX_REF_CODE` 空；主按钮 = base |
| P0 | 站内裂变（登录 → SharePanel → 复制 `?ref=` 链；新用户注册后 `ensureReferralCode` 生成自己的码） | PlanetX | ✅ 发码侧已有 | **当前唯一可跑的增长闭环意图**；接收方 `?ref=` 核销仍见 P1-阻塞 |
| **P1-阻塞** | **落地消费 `?ref=`**：解析 URL → 本地暂存 → 注册/登录成功后 `POST /v1/referral/use` | **PlanetX** | **❌ 未实现** | E2E：带码进入 → 新用户核销成功；与 `?join=` 舰队逻辑并存不互相覆盖 |
| P1 | Looma 渠道码：`max_uses` / `campaign`（或等价），避免一人占码 | Looma | ❌ 未实现 | 同一渠道码可服务 N 人；与一次性个人邀请码区分 |
| P1 | 为博主发码写入门户 env **仅在** P1-阻塞 +（若多人）渠道码落地之后 | 运营 | 阻塞中 | 否则只是装饰性 URL |
| P2 | 分享文案与门户对齐；可选 utm（仅当 PlanetX 真正读取时再加） | PlanetX / 门户 | 可选 | 不加装饰参数冒充归因 |

#### 8.2.3 纪律：一次性码 vs 渠道码

| 类型 | 定义 | 适用 | 禁止 |
|------|------|------|------|
| **一次性邀请码** | 当前 Looma 默认：`/use` 后 `used_by` 占用 | 站内一人一码裂变（发出方 SharePanel） | 当作全站博客 CTA 的固定 `VITE_PLANETX_REF_CODE` |
| **渠道 / 活动码** | 需新增：可多次核销或按 `max_uses` | 门户/投放「全站一个码」归因 | 在未改 Looma 前用真码冒充渠道码 |
| **验收假码** | 如 `portal-verify` | 只验门户拼 URL | 当作生产归因真源 |

**现阶段最小落地（与代码一致）：**

1. 门户主 CTA → `https://app.genz.ltd`（**不带 ref**；utm 暂不加——PlanetX 不读，加了也是装饰）。  
2. **当前唯一能跑通的闭环：** 登录 → SharePanel → 复制 `?ref=` 链 → 他人注册后 `ensureReferralCode` 生成自己的码（站内裂变）。  
3. `VITE_PLANETX_REF_CODE` **只留给验收演示**（点进能看到 `?ref=` 拼对即可，**不验证核销**）。  
4. 真要渠道归因，两条都动才有意义：PlanetX 落地消费 `?ref=`（注册成功后调 `/use`）+ Looma 加 `max_uses`/campaign 类型（否则归因码一人占掉）。

### 8.3 DemoPPI 共识增强层（待适配）

| 优先级 | 项 | Owner | 验收 |
|--------|----|--------|------|
| P0 | 独立域名上线（不占 `szbolent.cn`） | DemoPPI / 运维 | HTTPS 可访问；门户 `VITE_DEMOPPI_URL` 可配置 |
| P0 | 门户次 CTA 指向该域 | 门户 | 已支持；填 env 即可 |
| P1 | DemoPPI 内回链：门户博客 + 主 CTA 去 PlanetX | DemoPPI | Layer0 后或 `/p/[user]` 可见双链 |
| P1 | 对外叙事只保留一条顺序 | 策略 | 推荐：博客 →（可选）DemoPPI → PlanetX；材料不混称 |
| P2 | 个人页只读拉取门户最新博客（可选） | DemoPPI | 只读 WP REST；不做第二 CMS |
| 冻结 | Looma ↔ Supabase SSO | — | L2 以前不做 |
| 纪律 | 不把 Supabase 升格为主链数据面 | 策略 | 见 §8.0；非反 Supabase，反与自有云重复建设 |

### 8.4 排期建议（四步 · 已按阻塞项修正）

1. **定域** — PlanetX 落点 `https://app.genz.ltd`；DemoPPI 独立域写入 `VITE_DEMOPPI_URL`（可选）。
2. **门户 P0** — ✅ 已验收；**正式配置将 `VITE_PLANETX_REF_CODE` 留空**，主 CTA 只落点。
3. **人工路径** — 博客 → CTA 进 PlanetX →（可选）DemoPPI；站内用 SharePanel 体验「发码」；**勿宣称门户 ref 已归因**。
4. **再开渠道归因专项** — 先做 §8.2 **P1-阻塞**（PlanetX 消费 `?ref=`），需要多人同一码时再做 Looma 渠道码；完成前不把真实码写进门户生产 env。

### 8.5 冻结项（适配时勿破）

- 不在 WordPress 内实现邀请码系统
- 不把 DemoPPI 邀请当成 PlanetX 裂变真源
- 不合并三套账号
- 不为营销扩 Page Engine / 重 BFF
- **不为增长主链再建设第二套 BaaS 数据面**（Supabase 可留在增强层试验，不升格、不双写用户主数据）
- **不在 PlanetX 未消费 `?ref=` 前，把门户固定 ref 当成生产归因**
- **不在 Looma 仍为一次性核销时，用单码冒充全站渠道码**

---

*备忘收敛自 2026-08-11 团队对齐；§8.2 已与代码对齐：标注 PlanetX 落地未消费 `?ref=` 为 P1-阻塞，并区分一次性码 / 渠道码 / 验收假码。变更时同步 README 文档表。*
