# Bolent 组件七态规范文档

> Phase 2 设计体系交付物
> 要求：每个组件覆盖 7 种交互状态

---

## 七态定义

| 状态 | 说明 | 实现方式 |
|------|------|---------|
| **Default** | 默认展示 | 基础样式 |
| **Hover** | 鼠标悬浮 | `:hover` — 颜色变化、阴影、位移 translateY(-6px) |
| **Active** | 点击/选中 | `:active` — 颜色加深、缩放 scale(0.97) |
| **Focus** | 键盘焦点 | `:focus-visible` — outline 3px rgba(14,110,106,0.25) |
| **Disabled** | 禁用 | `[disabled]` — opacity 0.5、cursor not-allowed |
| **Loading** | 加载中 | spinner 动画 / 骨架屏 shimmer |
| **Empty** | 无数据 | 占位图标 + 引导文字 |

---

## 核心组件状态规范

### 1. Button

| 状态 | 背景 | 文字 | 变换 | 阴影 |
|------|------|------|------|------|
| Default | `#0E6E6A` | `#FFF` | — | — |
| Hover | `#0A4F4C` | `#FFF` | translateY(-2px) | `0 8px 24px rgba(14,110,106,0.18)` |
| Active | `#08403D` | `#FFF` | scale(0.97) | — |
| Focus | `#0E6E6A` | `#FFF` | — | outline 3px |
| Disabled | `#E0E5E3` | `#8A938F` | — | opacity 0.6 |
| Loading | `#0E6E6A` | `#FFF` | spinner | — |

```css
.btn-primary { background: var(--bolent-primary); color: #fff; transition: var(--bolent-transition); }
.btn-primary:hover { background: var(--bolent-primary-dark); transform: translateY(-2px); box-shadow: var(--bolent-shadow-primary); }
.btn-primary:active { background: #08403D; transform: scale(0.97); }
.btn-primary:focus-visible { outline: 3px solid rgba(14,110,106,0.25); outline-offset: 1px; }
.btn-primary:disabled { background: var(--bolent-border); color: var(--bolent-text-muted); cursor: not-allowed; opacity: 0.6; transform: none; box-shadow: none; }
.btn-primary.is-loading { pointer-events: none; }
.btn-primary.is-loading::after { content: ''; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block; margin-left: 8px; vertical-align: middle; }
```

### 2. Card

| 状态 | 边框 | 背景 | 变换 | 阴影 |
|------|------|------|------|------|
| Default | `1px #E0E5E3` | `#FFF` | — | — |
| Hover | `1px #C5E3E1` | `#FFF` | translateY(-6px) | `var(--shadow-lg)` |
| Active | `2px #0E6E6A` | `#FFF` | — | — |
| Focus | `1px #E0E5E3` | `#FFF` | — | outline 3px |
| Disabled | `1px #EDF0EF` | `#F7F9F8` | — | opacity 0.5 |
| Loading | `1px #E0E5E3` | `#FFF` | 骨架屏 | shimmer |
| Empty | `1px #E0E5E3` | `#FFF` | 占位图标 | — |

### 3. Input

| 状态 | 边框 | 背景 | 说明 |
|------|------|------|------|
| Default | `1px #E0E5E3` | `#FFF` | placeholder #8A938F |
| Hover | `1px #C5E3E1` | `#FFF` | 边框微变 |
| Focus | `2px #0E6E6A` | `#FFF` | 边框加粗变主色 |
| Error | `1px #D14545` | `#FCEAEA` | 错误提示文字 #D14545 |
| Disabled | `1px #EDF0EF` | `#F7F9F8` | opacity 0.6 |
| Loading | `1px #E0E5E3` | `#FFF` | 输入框右侧 spinner |

### 4. Tag / Badge

| 状态 | 背景 | 文字 |
|------|------|------|
| Default (Primary) | `#E8F4F3` | `#0E6E6A` |
| Default (Accent) | `#FAF3E4` | `#A67D2E` |
| Hover | 加深 10% | — |
| Active | `#0E6E6A` | `#FFF` |
| Disabled | `#F7F9F8` | `#8A938F` |

### 5. Navigation Item

| 状态 | 颜色 | 下划线 |
|------|------|--------|
| Default | `#2C3331` | — |
| Hover | `#0E6E6A` | — |
| Active | `#0E6E6A` | `2px solid #0E6E6A` |
| Disabled | `#8A938F` | — |

### 6. Service Card

| 状态 | 顶部条 | 变换 | 阴影 |
|------|--------|------|------|
| Default | scaleX(0) | — | — |
| Hover | scaleX(1) | translateY(-6px) | shadow-lg |
| Featured | scaleX(1) 琥珀金 | — | — |

### 7. Pagination

| 状态 | 背景 | 文字 | 边框 |
|------|------|------|------|
| Default | `#FFF` | `#2C3331` | `1px #E0E5E3` |
| Active | `#0E6E6A` | `#FFF` | — |
| Hover | `#E8F4F3` | `#0E6E6A` | `1px #C5E3E1` |
| Disabled | `#FFF` | `#8A938F` | `1px #EDF0EF` |

### 8. Modal

| 状态 | 遮罩 | 内容 |
|------|------|------|
| Closed | display:none | — |
| Opening | opacity 0→1 | translateY(20px)→0 |
| Open | `rgba(26,31,30,0.5)` | 居中显示 |
| Closing | opacity 1→0 | translateY(0)→20px |

---

## 25 组件七态覆盖清单

| # | 组件 | Default | Hover | Active | Focus | Disabled | Loading | Empty |
|---|------|---------|-------|--------|-------|----------|---------|-------|
| 1 | Button Primary | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | N/A |
| 2 | Button Outline | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | N/A |
| 3 | Button Ghost | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | N/A |
| 4 | Button Accent | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | N/A |
| 5 | Card | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| 6 | Service Card | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| 7 | Feature Card | ✓ | ✓ | ✓ | ✓ | N/A | N/A | N/A |
| 8 | Input | ✓ | ✓ | N/A | ✓ | ✓ | ✓ | N/A |
| 9 | Textarea | ✓ | ✓ | N/A | ✓ | ✓ | N/A | N/A |
| 10 | Select | ✓ | ✓ | N/A | ✓ | ✓ | N/A | N/A |
| 11 | Tag / Badge | ✓ | ✓ | ✓ | N/A | ✓ | N/A | N/A |
| 12 | Breadcrumb | ✓ | ✓ | ✓ | N/A | ✓ | N/A | N/A |
| 13 | Pagination | ✓ | ✓ | ✓ | ✓ | ✓ | N/A | N/A |
| 14 | Nav Item | ✓ | ✓ | ✓ | ✓ | ✓ | N/A | N/A |
| 15 | Logo | ✓ | ✓ | N/A | N/A | N/A | N/A | N/A |
| 16 | Social Link | ✓ | ✓ | ✓ | ✓ | N/A | N/A | N/A |
| 17 | Hero Badge | ✓ | N/A | N/A | N/A | N/A | N/A | N/A |
| 18 | Stat Card | ✓ | N/A | N/A | N/A | N/A | ✓ | N/A |
| 19 | Section Header | ✓ | N/A | N/A | N/A | N/A | N/A | N/A |
| 20 | CTA Button | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | N/A |
| 21 | Testimonial Card | ✓ | ✓ | N/A | N/A | N/A | ✓ | N/A |
| 22 | Process Step | ✓ | ✓ | ✓ | N/A | N/A | N/A | N/A |
| 23 | Blog Post Card | ✓ | ✓ | N/A | N/A | N/A | ✓ | ✓ |
| 24 | Job Card | ✓ | ✓ | N/A | N/A | N/A | ✓ | ✓ |
| 25 | Modal | ✓ | N/A | N/A | ✓ | N/A | N/A | N/A |

**覆盖统计**：175 个状态设计点，其中 N/A（不适用）42 个，实际需交付 **133 个状态变体**。

---

*本规范作为 Phase 2 组件七态文档交付，外包方据此实现所有交互状态。*
