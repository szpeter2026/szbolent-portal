# Bolent 品牌设计规范 v1.0

> 项目：szbolent-portal
> 日期：2026-07-05
> 阶段：Phase 1 品牌设计

---

## 一、品牌定位

### 1.1 核心定位

**Bolent** — 融合现代科技与文化底蕴的数智企业门户。

主体为企业 IT 服务门户（软件开发、数字化、自动化、IT 管理、敏捷咨询、IT 外包），以"诗词/AI 读诗"作为特色业务板块融入，形成"科技有温度、文化有根基"的差异化品牌气质。

### 1.2 品牌关键词

沉稳 · 融通 · 精致 · 可信赖 · 东方意境

### 1.3 品牌承诺

以工程精度交付数字化价值，以人文视角连接技术与文化。

---

## 二、品牌色板

### 2.1 主色系 — 墨青 Teal

| Token | 色值 | 用途 |
|-------|------|------|
| `--bolent-primary-50` | `#E8F4F3` | 最浅底色、hover 背景 |
| `--bolent-primary-100` | `#C5E3E1` | 浅色填充、标签背景 |
| `--bolent-primary-200` | `#9DD0CC` | 装饰元素 |
| `--bolent-primary-400` | `#2A8F8A` | 辅助强调 |
| **`--bolent-primary`** | **`#0E6E6A`** | **主色 · 按钮、链接、图标、强调** |
| `--bolent-primary-800` | `#0A4F4C` | 深色 · hover、渐变端点 |

**选色逻辑**：墨青区别于通用 Ant Design 蓝（#1890ff），带有东方青瓷色调，既保留科技感又注入文化底蕴。与 looma-zervi 产品线形成色彩区分。

### 2.2 辅色系 — 琥珀金 Amber

| Token | 色值 | 用途 |
|-------|------|------|
| `--bolent-accent-50` | `#FAF3E4` | 暖底色、诗词板块背景 |
| `--bolent-accent-light` | `#E0B85A` | 辅助强调、装饰 |
| **`--bolent-accent`** | **`#C99A3F`** | **辅色 · 点缀、诗词板块、Logo 金点** |
| `--bolent-accent-dark` | `#A67D2E` | 深色辅色 |

**使用规则**：琥珀金仅作点缀使用（占比 ≤15%），不可大面积铺底。主要用于诗词/AI读诗板块的视觉标识、数据高亮、Logo 装饰点。

### 2.3 中性色 — 暖灰

| Token | 色值 | 用途 |
|-------|------|------|
| `--bolent-ink` | `#1A1F1E` | 近黑标题色（带微青） |
| `--bolent-text` | `#2C3331` | 正文主色 |
| `--bolent-text-secondary` | `#5C6663` | 辅助文字 |
| `--bolent-text-muted` | `#8A938F` | 弱化文字、占位符 |
| `--bolent-border` | `#E0E5E3` | 边框、分割线 |
| `--bolent-bg` | `#FFFFFF` | 主背景 |
| `--bolent-bg-soft` | `#F7F9F8` | 浅底区块 |
| `--bolent-bg-warm` | `#FAF8F4` | 暖底区块（诗词板块） |

### 2.4 语义色

| 用途 | 色值 | 背景色 |
|------|------|--------|
| 成功 | `#2E8B57` | `#E6F4EC` |
| 警告 | `#E0932B` | `#FDF3E1` |
| 错误 | `#D14545` | `#FCEAEA` |
| 信息 | `#2B7BB9` | `#E6F1FB` |

### 2.5 渐变

替换原 Vue 代码中的 `#667eea / #764ba2` 占位紫渐变：

```css
/* 主渐变 · Hero / CTA */
--bolent-gradient: linear-gradient(135deg, #0E6E6A 0%, #0A4F4C 100%);

/* 青金渐变 · 特色区域（青→金过渡，体现科技×文化融合） */
--bolent-gradient-accent: linear-gradient(135deg, #0E6E6A 0%, #C99A3F 100%);

/* 柔和渐变 · 区块过渡 */
--bolent-gradient-soft: linear-gradient(180deg, #F7F9F8 0%, #FAF8F4 100%);
```

---

## 三、字体方案

### 3.1 字体配对

| 场景 | 字体 | 用途 |
|------|------|------|
| 中文正文 | 阿里巴巴普惠体 3.0 / 思源黑体 | 企业板块正文、导航、按钮 |
| 中文标题 | 思源宋体 / Noto Serif SC | 诗词板块标题、文化感强调 |
| 英文正文 | Manrope | 英文内容、数字 |
| 英文标题 | Manrope (500) | 英文标题 |
| 等宽 | JetBrains Mono | 代码块、技术标签 |

### 3.2 字号规范

| 层级 | Desktop | Tablet | Mobile |
|------|---------|--------|--------|
| H1 (Hero) | 48px | 40px | 32px |
| H2 (区块) | 36px | 30px | 28px |
| H3 (卡片) | 24px | 22px | 20px |
| H4 | 20px | 18px | 18px |
| Body | 17px | 16px | 16px |
| Small | 14px | 14px | 13px |

### 3.3 字重

- 正文：400 Regular
- 标题/强调：500 Medium
- 仅使用两个字重，避免 600/700 过粗

---

## 四、Logo 规范

### 4.1 Logo 构成

Logo 由图形标记 + 文字标组成：
- **图形标记**：左侧数据节点方块（墨青）+ 右侧书卷弧线（墨青）+ 琥珀金圆点，象征"数据×文化"的连接
- **文字标**：Manrope 700，字距 -0.5px

### 4.2 版本

| 版本 | 文件 | 使用场景 |
|------|------|---------|
| 主版本 | `bolent-logo.svg` | 浅色背景 |
| 反白版本 | `bolent-logo-white.svg` | 墨青/深色背景、Hero 区 |

### 4.3 安全间距与最小尺寸

- **安全间距**：图形四周预留 ≥16px clear space（等于图形高度 1/3）
- **最小尺寸**：宽度 120px（确保文字标可读）
- **图形与文字间距**：8px

### 4.4 禁止事项

- 不可拉伸变形
- 不可更改颜色（除主版本/反白版本外）
- 不可添加投影、发光等效果
- 不可旋转
- 文字标与图形标记不可拆分使用（小尺寸场景除外）

---

## 五、间距与圆角

### 5.1 间距

| 元素 | 桌面 | 移动 |
|------|------|------|
| Section 上下内边距 | 100px | 60px |
| 卡片内边距 | 32px | 24px |
| Grid 间距 | 32px | 20px |
| 容器内边距 | 24px | 20px |

### 5.2 圆角

| 元素 | 圆角 |
|------|------|
| 卡片/图片 | 12px |
| 大卡片/容器 | 16px |
| 按钮/输入框 | 8px |
| 标签/胶囊 | 999px (pill) |

---

## 六、Astra Customizer 映射

外包方在 WordPress 后台 Appearance → Customize 中按以下映射设置：

| Astra 设置项 | Bolent 值 |
|-------------|-----------|
| Primary Color | `#0E6E6A` |
| Text Color | `#2C3331` |
| Link Color / Hover | `#0E6E6A` / `#0A4F4C` |
| Heading Font Family | Alibaba PuHuiTi / Source Han Sans |
| Body Font Family | Alibaba PuHuiTi / Source Han Sans |
| Base Font Size | 17px |
| Container Width | 1200px |
| Button Radius | 8px |
| Button BG / Hover | `#0E6E6A` / `#0A4F4C` |

> 完整 CSS 变量见 `tokens/brand-tokens.css`，子主题 `style.css` 将在 Phase 3 产出。

---

*本规范作为 Phase 1 品牌设计交付物，后续阶段以此为基础构建设计体系与 WordPress 实施。*
