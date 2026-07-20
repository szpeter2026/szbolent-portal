# szbolent-portal 运维手册

> **版本：** v1.0  
> **最后更新：** 2026-07-20  
> **适用范围：** szbolent-portal 全栈（Vue3 前端 + WordPress Headless + Looma API）  
> **前置阅读：** README.md、docs/DUAL_REPO_WORK_GUIDE.md、docs/TENCENT_CLOUD_COMMERCE.md

---

## 目录

1. [基础设施拓扑](#1-基础设施拓扑)
2. [本地开发环境搭建](#2-本地开发环境搭建)
3. [生产环境部署](#3-生产环境部署)
4. [日常运维操作](#4-日常运维操作)
5. [监控与排障](#5-监控与排障)
6. [备份与恢复](#6-备份与恢复)
7. [应急预案](#7-应急预案)
8. [运维验证清单](#8-运维验证清单)

---

## 1. 基础设施拓扑

### 1.1 服务器清单

| 标识 | IP | 云厂商 | 角色 | 关键进程 |
|------|-----|--------|------|---------|
| `portal` | `47.115.168.107` | 阿里云 ECS | 门户机 | Nginx, WordPress Docker, Vue3 静态文件 |
| `api-proxy` | `api.genz.ltd` | 腾讯云 | API 入口 | Nginx → Looma |
| `looma` | `1.14.202.161` | — | 后端 | Looma API (:5200) |

### 1.2 请求流转全链路

```
用户浏览器
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│ 47.115.168.107:80 (Nginx)                                   │
│                                                             │
│  location / {                                               │
│    proxy_pass http://127.0.0.1:8080;  ← WordPress Docker   │
│  }                                                          │
│                                                             │
│  location /v1/ {                                            │
│    proxy_pass http://api.genz.ltd/v1/;  ← Looma API      │
│  }                                                          │
└─────────────────────────────────────────────────────────────┘
                │                           │
                ▼                           ▼
┌───────────────────────┐    ┌────────────────────────────────┐
│ WordPress Docker      │    │ api.genz.ltd:80 (Nginx)      │
│ MySQL 8.0 + WP 6.7   │    │   └─► 1.14.202.161:5200        │
│ 127.0.0.1:8080        │    │      (Looma Python 后端)       │
└───────────────────────┘    └────────────────────────────────┘
```

### 1.3 关键目录（门户机 `47.115.168.107`）

| 路径 | 内容 |
|------|------|
| `/var/www/szbolent-portal/dist/` | Vue3 前端构建产物 |
| `/var/www/szbolent-portal/nginx/szbolent.conf` | 前端 Nginx 配置 |
| `/opt/bolent-wp/` | WordPress Docker 根目录 |
| `/opt/bolent-wp/docker-compose.wp.prod.yml` | WordPress 编排文件 |
| `/opt/bolent-wp/.env` | 数据库密码等敏感配置（600 权限） |
| `/opt/bolent-wp/themes/bolent-astra-child/` | Bolent 子主题（只读挂载） |
| `/etc/nginx/conf.d/szbolent.conf` | 生效的 Nginx 站点配置（RHEL 系） |

### 1.4 数据流架构（双通道）

```
┌─────────────────── 前端 (Vue3 Portal) ─────────────────────┐
│                                                            │
│   ┌─────────────────────┐    ┌──────────────────────────┐  │
│   │  Looma API 通道      │    │  WordPress 独立通道       │  │
│   │  (api.genz.ltd)     │    │  (/wp-json/wp/v2/*)     │  │
│   ├─────────────────────┤    ├──────────────────────────┤  │
│   │ • /v1/poetry/*      │    │ • 文章/博客列表          │  │
│   │ • /v1/search        │    │ • 页面内容               │  │
│   │ • /v1/ask (AI问答)  │    │ • 联系表单 CF7           │  │
│   │ • /v1/auth/*        │    │ • 导航菜单               │  │
│   │ • /v1/payment/*     │    │                        │  │
│   └─────────┬───────────┘    └────────────┬─────────────┘  │
│             │                              │                │
└─────────────┼──────────────────────────────┼────────────────┘
              ▼                              ▼
    ┌──────────────────┐         ┌──────────────────┐
    │  Looma Flask     │         │  WordPress REST  │
    │  (1.14.202.161)  │         │  (:8080 Docker)  │
    │  + ChromaDB RAG  │         │  + MySQL 8.0     │
    └──────────────────┘         └──────────────────┘
```

**设计原则：**
- **Looma 通道**：结构化数据（诗词、用户、支付、AI），多端共享契约
- **WP 通道**：CMS 内容（文章、页面、菜单），运营人员通过 WP 后台独立管理，不经过 Looma
- **两通道独立部署、互不阻塞**——WP 发文章不影响 API 可用性，反之亦然

---

## 2. 本地开发环境搭建

### 2.1 前置条件

- Node.js ≥ 18（推荐 22）
- Git（需要访问 GitHub + Gitee 双远程）
- Docker Desktop（可选，仅本地 WP 联调需要）

### 2.2 初始化步骤

```bash
# Step 1: 克隆仓库
git clone git@github.com:szpeter2026/szbolent-portal.git
cd szbolent-portal

# Step 2: 配置双远程（如需推送）
bash scripts/sync-remotes.sh setup

# Step 3: 安装依赖
npm install

# Step 4: 创建本地环境变量
cp .env.example .env.local

# Step 5: 启动开发服务器
npm run dev
# → 浏览器自动打开 http://localhost:3000
```

### 2.3 启动本地 WordPress（可选）

```bash
# 启动 WordPress Docker（含 MySQL）
docker compose -f docker-compose.wp.yml up -d

# 验证
curl http://localhost:8800/wp-json/wp/v2/posts

# WP 后台
open http://localhost:8800/wp-admin
# 默认账号密码需通过安装向导设置（仅首次）
```

### 2.4 开发环境验证

| 验证项 | 命令 | 预期结果 |
|--------|------|---------|
| 前端启动 | `curl -s http://localhost:3000 \| head` | 返回 HTML 页面 |
| Vite 代理 WP | `curl http://localhost:3000/wp-json/wp/v2/posts` | 返回 JSON（如 WP 未启动则 502，属正常） |
| Vite 代理 Looma | `curl http://localhost:3000/v1/` | 返回 Looma API 响应（如本地无 Looma，可忽略） |
| TypeScript 编译 | `npm run typecheck` | 无错误输出 |
| 生产构建 | `npm run build` | 生成 `dist/` 目录 |

---

## 3. 生产环境部署

### 3.1 部署前检查

```bash
# ① 确认当前分支
git branch --show-current
# 预期: main

# ② 确认工作区干净
git status
# 预期: nothing to commit, working tree clean

# ③ 确认最新代码已同步
git fetch origin && git log --oneline -3

# ④ TypeScript 类型检查通过
npm run typecheck

# ⑤ 生产构建成功
npm run build
# 确认 dist/ 目录生成且包含 index.html
```

### 3.2 前端部署（Vue3 SPA → Nginx 静态服务）

```bash
# 方式一：一键部署脚本
npm run deploy
# 等价于: bash scripts/deploy.sh

# 方式二：手动分步执行
npm run build
rsync -avz --delete dist/ root@47.115.168.107:/var/www/szbolent-portal/dist/
scp nginx.conf root@47.115.168.107:/var/www/szbolent-portal/nginx/szbolent.conf
ssh root@47.115.168.107 "
  cp /var/www/szbolent-portal/nginx/szbolent.conf /etc/nginx/conf.d/szbolent.conf
  rm -f /etc/nginx/conf.d/default.conf
  nginx -t && systemctl reload nginx
"
```

### 3.3 部署后验证

```bash
# ① HTTP 状态码检查
curl -o /dev/null -s -w '%{http_code}' http://47.115.168.107/
# 预期: 200

# ② 前端页面能正常加载
curl -s http://47.115.168.107/ | grep -o '<div id="app">'
# 预期: <div id="app">

# ③ WordPress API 可访问
curl -s http://47.115.168.107/wp-json/wp/v2/posts | head -c 200
# 预期: 返回 JSON 数据

# ④ Looma API 代理可达
curl -s -o /dev/null -w '%{http_code}' http://47.115.168.107/v1/
# 预期: 200 或 404（API 根路径），非 502/504 即表示代理链正常
```

### 3.4 WordPress 部署（内容管理后台）

```bash
# 首次全新部署
bash scripts/deploy-wp-aliyun.sh

# 后续更新子主题
rsync -avz design-system/phase3-wordpress/astra-child/ \
  root@47.115.168.107:/opt/bolent-wp/themes/bolent-astra-child/
```

### 3.5 WordPress 部署后手动配置（仅首次）

部署脚本完成后，浏览器操作：

1. 打开 `http://47.115.168.107/wp-admin/install.php`
2. 填写站点标题、管理员用户名/密码/邮箱
3. 登录后台 → **外观 → 主题**
4. 搜索并安装 **Astra** 父主题
5. 启用 **Bolent Astra Child** 子主题
6. **插件 → 安装插件** → 搜索安装 **Contact Form 7**
7. **设置 → 固定链接** → 选择「文章名」→ 保存更改

---

## 4. 日常运维操作

### 4.1 代码推送（双远程同步）

```bash
# 检查双远程状态
npm run sync:check

# 推送到 Gitee + GitHub
npm run sync:push

# 强制同步（谨慎使用）
npm run sync:force
```

### 4.2 Docker 容器管理（在门户机上执行）

```bash
# SSH 登录门户机
ssh root@47.115.168.107

# 进入 WordPress 目录
cd /opt/bolent-wp

# 查看容器状态
docker compose -f docker-compose.wp.prod.yml ps
# 预期: bolent_wp_mysql (healthy), bolent_wp (Up)

# 查看实时日志
docker compose -f docker-compose.wp.prod.yml logs -f --tail=100

# 重启 WordPress 容器
docker compose -f docker-compose.wp.prod.yml restart wordpress

# 重启 MySQL 容器
docker compose -f docker-compose.wp.prod.yml restart mysql

# 全部重启
docker compose -f docker-compose.wp.prod.yml down && \
docker compose -f docker-compose.wp.prod.yml up -d
```

### 4.3 Nginx 管理（在门户机上执行）

```bash
# 检查配置语法
nginx -t

# 重载配置（不中断服务）
systemctl reload nginx

# 重启 Nginx
systemctl restart nginx

# 查看 Nginx 状态
systemctl status nginx

# 查看访问日志（最近 50 条）
tail -n 50 /var/log/nginx/access.log

# 查看错误日志
tail -n 50 /var/log/nginx/error.log

# 实时监控访问
tail -f /var/log/nginx/access.log
```

### 4.4 WordPress 故障自愈检查

```bash
ssh root@47.115.168.107 << 'EOF'
# ① Docker 服务是否运行
systemctl is-active docker || systemctl start docker

# ② WordPress 容器是否存活
docker ps --filter name=bolent_wp --format '{{.Status}}' | grep -q Up || \
  (cd /opt/bolent-wp && docker compose -f docker-compose.wp.prod.yml up -d)

# ③ MySQL 健康检查
docker exec bolent_wp_mysql mysqladmin ping -h localhost -uroot -p"$(grep MYSQL_ROOT_PASSWORD /opt/bolent-wp/.env | cut -d= -f2)" 2>/dev/null && echo "MySQL OK" || echo "MySQL FAIL"

# ④ 磁盘空间检查
df -h / | tail -1 | awk '{print "磁盘使用: "$5" (剩余: "$4")"}'

# ⑤ 内存检查
free -h | grep Mem | awk '{print "内存: 已用 "$3" / 总计 "$2}'
EOF
```

### 4.5 定期维护检查清单

| 频率 | 操作 | 命令 |
|------|------|------|
| **每日** | 检查容器运行状态 | `docker ps --filter name=bolent_` |
| **每日** | 检查磁盘使用率 | `df -h /` |
| **每周** | 检查 Nginx 错误日志 | `tail -50 /var/log/nginx/error.log` |
| **每周** | 数据库备份（见 §6） | — |
| **每月** | Docker 镜像清理 | `docker system prune -a` |
| **每月** | WordPress 核心/插件更新 | WP 后台 → 仪表盘 → 更新 |

---

## 5. 监控与排障

### 5.1 快速诊断流程

当用户反馈「网站打不开」时，按以下顺序排查：

```
① 前端静态资源
  curl -o /dev/null -s -w '%{http_code}' http://47.115.168.107/
  → 非 200: 检查 Nginx 是否运行，dist/ 是否存在

② WordPress REST API
  curl http://47.115.168.107/wp-json/wp/v2/posts
  → 502/504: WordPress 容器可能挂了

③ Looma API 代理
  curl http://47.115.168.107/v1/
  → 502/504: 检查 api.genz.ltd 或 1.14.202.161 是否可达
```

### 5.2 常见故障及处理

#### 故障 A：网站返回 502 Bad Gateway

```bash
# 1. 检查 Nginx 错误日志
ssh root@47.115.168.107 'tail -20 /var/log/nginx/error.log'

# 2. 常见原因：WordPress 容器未运行
ssh root@47.115.168.107 'docker ps | grep bolent_wp'
# 如果没有输出 → 容器已停止

# 3. 重启容器
ssh root@47.115.168.107 'cd /opt/bolent-wp && docker compose -f docker-compose.wp.prod.yml up -d'

# 4. 如果容器无法启动，查看日志
ssh root@47.115.168.107 'cd /opt/bolent-wp && docker compose -f docker-compose.wp.prod.yml logs --tail=50'
```

#### 故障 B：博客列表/详情页无内容

```bash
# 1. 检查 WordPress REST API
curl http://47.115.168.107/wp-json/wp/v2/posts

# 2. 返回空数组 [] → WP 中确实没有文章，登录后台检查
# 3. 返回 404 → 固定链接可能未设置，登录 WP 后台重新保存「设置 → 固定链接」
# 4. 返回 HTML 而非 JSON → Nginx 路由问题
```

#### 故障 C：诗词功能不可用

```bash
# 1. 检查 Looma API 代理
curl -s http://47.115.168.107/v1/poetry/

# 2. 如果返回 502，检查中间代理机
#    SSH 到 api.genz.ltd，确认 Nginx 和到 1.14.202.161 的连通性

# 3. 如果中间代理正常，检查 Looma 后端
#    SSH 到 1.14.202.161
#    systemctl status looma  (或实际的服务名)
#    curl http://127.0.0.1:5200/v1/
```

#### 故障 D：前端页面白屏（JS 报错）

```bash
# 1. 检查 dist/ 文件是否完整
ssh root@47.115.168.107 'ls -la /var/www/szbolent-portal/dist/'

# 2. 检查 index.html 是否存在
ssh root@47.115.168.107 'cat /var/www/szbolent-portal/dist/index.html | head -5'

# 3. 重新构建部署
npm run build && npm run deploy
```

#### 故障 E：SSL/HTTPS 问题（备案后）

```bash
# 使用 Let's Encrypt 申请免费证书
ssh root@47.115.168.107
dnf install -y certbot python3-certbot-nginx
certbot --nginx -d szbolent.cn -d www.szbolent.cn
# 测试自动续期
certbot renew --dry-run
```

#### 故障 F：磁盘空间不足

```bash
# 清理 Docker 无用镜像和卷
docker system prune -a --volumes -f

# 清理系统日志
journalctl --vacuum-size=200M

# 检查大文件
du -sh /var/lib/docker/volumes/*
du -sh /var/log/*
```

---

## 6. 备份与恢复

### 6.1 MySQL 数据库备份

```bash
# 在门户机上执行
ssh root@47.115.168.107

# 获取密码
MYSQL_ROOT_PW=$(grep MYSQL_ROOT_PASSWORD /opt/bolent-wp/.env | cut -d= -f2)

# 导出数据库
BACKUP_FILE="/root/backups/bolent_wp_$(date +%Y%m%d_%H%M%S).sql"
mkdir -p /root/backups
docker exec bolent_wp_mysql mysqldump \
  -uroot -p"${MYSQL_ROOT_PW}" \
  --single-transaction \
  --routines \
  --triggers \
  bolent_wp > "${BACKUP_FILE}"

# 压缩
gzip "${BACKUP_FILE}"
echo "备份完成: ${BACKUP_FILE}.gz"

# 拉取到本地
exit  # 退出 SSH
scp root@47.115.168.107:/root/backups/bolent_wp_*.sql.gz ./backups/
```

### 6.2 数据库恢复

```bash
# 1. 上传备份到服务器
scp ./backups/bolent_wp_20260720.sql.gz root@47.115.168.107:/root/backups/

# 2. SSH 到服务器
ssh root@47.115.168.107

# 3. 解压
gunzip /root/backups/bolent_wp_20260720.sql.gz

# 4. 获取密码
MYSQL_ROOT_PW=$(grep MYSQL_ROOT_PASSWORD /opt/bolent-wp/.env | cut -d= -f2)

# 5. 恢复（会覆盖现有数据！）
docker exec -i bolent_wp_mysql mysql \
  -uroot -p"${MYSQL_ROOT_PW}" bolent_wp < /root/backups/bolent_wp_20260720.sql

# 6. 清理 WordPress 缓存（如有缓存插件）
docker exec bolent_wp wp cache flush --allow-root 2>/dev/null || true
```

### 6.3 文件备份

```bash
# 备份上传的媒体文件（wp-content/uploads）
ssh root@47.115.168.107 '
  cd /opt/bolent-wp && \
  docker compose -f docker-compose.wp.prod.yml exec -T wordpress \
    tar czf - /var/www/html/wp-content/uploads > /root/backups/wp_uploads_$(date +%Y%m%d).tar.gz
'

# 拉取到本地
scp root@47.115.168.107:/root/backups/wp_uploads_*.tar.gz ./backups/
```

### 6.4 自动备份脚本（添加到 crontab）

```bash
# 在门户机上创建 /root/backup.sh
cat > /root/backup.sh << 'SCRIPT'
#!/bin/bash
set -euo pipefail
BACKUP_DIR="/root/backups"
MYSQL_ROOT_PW=$(grep MYSQL_ROOT_PASSWORD /opt/bolent-wp/.env | cut -d= -f2)
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p "$BACKUP_DIR"

# MySQL 备份
docker exec bolent_wp_mysql mysqldump -uroot -p"${MYSQL_ROOT_PW}" --single-transaction bolent_wp | gzip > "${BACKUP_DIR}/bolent_wp_${DATE}.sql.gz"

# 保留最近 7 天的备份
find "$BACKUP_DIR" -name 'bolent_wp_*.sql.gz' -mtime +7 -delete
echo "Backup completed: bolent_wp_${DATE}.sql.gz"
SCRIPT

chmod +x /root/backup.sh

# 添加定时任务（每天凌晨 3 点执行）
echo '0 3 * * * /root/backup.sh >> /var/log/backup.log 2>&1' | crontab -
```

---

## 7. 应急预案

### 7.1 门户机完全故障恢复步骤

假设阿里云 ECS 被重置或需要迁移到新机器，执行以下步骤恢复全部服务：

```bash
# === Step 1: 安装基础依赖 ===
ssh root@<新服务器IP>
dnf install -y nginx git curl openssl

# === Step 2: 一键部署 WordPress ===
curl -fsSL https://raw.githubusercontent.com/szpeter2026/szbolent-portal/main/scripts/bootstrap-wp-on-server.sh | bash

# === Step 3: 部署前端 ===
# 在本地执行
npm run build
rsync -avz --delete dist/ root@<新服务器IP>:/var/www/szbolent-portal/dist/
scp nginx.conf root@<新服务器IP>:/var/www/szbolent-portal/nginx/szbolent.conf

# === Step 4: 配置 Nginx ===
ssh root@<新服务器IP> '
  cp /var/www/szbolent-portal/nginx/szbolent.conf /etc/nginx/conf.d/szbolent.conf
  rm -f /etc/nginx/conf.d/default.conf
  nginx -t && systemctl reload nginx
'

# === Step 5: 恢复数据库（如有备份）===
# 参考 §6.2 数据库恢复步骤

# === Step 6: 验证 ===
# 参考 §8 运维验证清单
```

### 7.2 Nginx 配置回滚

```bash
# 如果新配置有问题，回滚到备份
ssh root@47.115.168.107 '
  # 备份当前配置
  cp /etc/nginx/conf.d/szbolent.conf /etc/nginx/conf.d/szbolent.conf.bak.$(date +%Y%m%d%H%M%S)
  
  # 从 Git 历史恢复或恢复上一次的备份
  ls -t /etc/nginx/conf.d/szbolent.conf.bak.* | head -2
  cp /etc/nginx/conf.d/szbolent.conf.bak.XXXXXXXX /etc/nginx/conf.d/szbolent.conf
  nginx -t && systemctl reload nginx
'
```

### 7.3 Docker 容器无法启动的终极方案

```bash
ssh root@47.115.168.107 '
  # 完全清理并重建
  cd /opt/bolent-wp
  docker compose -f docker-compose.wp.prod.yml down -v  # ⚠️ 会删除数据卷！
  # 如果有数据库备份，先恢复再执行以上命令
  docker compose -f docker-compose.wp.prod.yml up -d
'
```

---

## 8. 运维验证清单

> **使用说明：** 每次部署/变更后，逐项执行并打勾。全部通过才算完成一次合格运维操作。

### 8.1 前端部署验证

- [ ] **HTTP 状态码**：`curl -o /dev/null -s -w '%{http_code}' http://47.115.168.107/` → 返回 `200`
- [ ] **页面加载**：浏览器打开 `http://47.115.168.107/`，首页正常渲染，无白屏
- [ ] **SPA 路由**：浏览器访问 `http://47.115.168.107/about`，关于页面正常显示（非 404）
- [ ] **博客列表**：浏览器访问 `http://47.115.168.107/blog`，博客列表正常加载
- [ ] **控制台无报错**：F12 打开开发者工具，Console 面板无红色错误
- [ ] **响应头正确**：`curl -I http://47.115.168.107/ 2>/dev/null | grep -i content-type` → `text/html`

### 8.2 WordPress 服务验证

- [ ] **REST API 可达**：`curl -s http://47.115.168.107/wp-json/wp/v2/posts` → 返回 JSON 数组
- [ ] **后台可登录**：浏览器打开 `http://47.115.168.107/wp-admin`，能用管理员账号登录
- [ ] **主题已启用**：后台 → 外观 → 主题 → 确认 **Bolent Astra Child** 为当前主题
- [ ] **固定链接正确**：`curl -s http://47.115.168.107/wp-json/` → 返回 API 索引（非 404）

### 8.3 Docker 容器健康验证

- [ ] **容器运行**：`ssh root@47.115.168.107 'docker ps --filter name=bolent_wp --format "{{.Status}}"'` → 含 `Up`
- [ ] **MySQL 容器运行**：`ssh root@47.115.168.107 'docker ps --filter name=bolent_wp_mysql --format "{{.Status}}"'` → 含 `healthy`
- [ ] **MySQL 内置检查**：`ssh root@47.115.168.107 "docker exec bolent_wp_mysql mysqladmin ping -h localhost -uroot -p$(grep MYSQL_ROOT_PASSWORD /opt/bolent-wp/.env | cut -d= -f2)"` → `mysqld is alive`
- [ ] **WordPress 到 MySQL 连通**：访问 WP 后台正常（不报数据库连接错误）

### 8.4 Looma API 代理验证

- [ ] **代理可达**：`curl -s -o /dev/null -w '%{http_code}' http://47.115.168.107/v1/` → 非 `502`/`504`
- [ ] **诗词 API**：`curl -s http://47.115.168.107/v1/poetry/` → 有 JSON 响应（可能是空数组或结果数组）
- [ ] **Nginx 代理配置**：`ssh root@47.115.168.107 'grep -A5 "location /v1/" /etc/nginx/conf.d/szbolent.conf' 2>/dev/null || ssh root@47.115.168.107 'grep -A5 "location /v1/" /etc/nginx/conf.d/bolent-wp.conf'` → 包含 `proxy_pass http://api.genz.ltd`

### 8.5 服务器基础健康验证

- [ ] **磁盘**：`ssh root@47.115.168.107 'df -h / | tail -1'` → 使用率 < 80%
- [ ] **内存**：`ssh root@47.115.168.107 'free -h | grep Mem'` → available > 500M
- [ ] **CPU**：`ssh root@47.115.168.107 'uptime'` → load average < CPU 核心数
- [ ] **Nginx 运行**：`ssh root@47.115.168.107 'systemctl is-active nginx'` → `active`
- [ ] **Docker 运行**：`ssh root@47.115.168.107 'systemctl is-active docker'` → `active`
- [ ] **备份存在**：`ssh root@47.115.168.107 'ls -lt /root/backups/ | head -3'` → 有最近备份文件

### 8.6 完整验证一键脚本

```bash
#!/bin/bash
# 在本地执行，验证整个部署链路
set -euo pipefail

SERVER="root@47.115.168.107"
PASS=0
FAIL=0

check() {
  local name="$1"
  local cmd="$2"
  echo -n "  [$name] ... "
  if eval "$cmd" &>/dev/null; then
    echo "✅ PASS"
    ((PASS++))
  else
    echo "❌ FAIL"
    ((FAIL++))
  fi
}

echo "=== szbolent-portal 运维验证 ==="
echo "时间: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""

echo "--- 前端 ---"
check "首页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/)" = "200"'
check "博客页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/blog)" = "200"'
check "关于页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/about)" = "200"'

echo ""
echo "--- WordPress ---"
check "REST API 正常" \
  "curl -s http://47.115.168.107/wp-json/wp/v2/posts | grep -q '\\['"
check "WP 后台可访问" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/wp-admin)" = "200"'

echo ""
echo "--- Looma API ---"
check "Looma 代理可达" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/v1/)" != "502"'

echo ""
echo "--- 服务器 ---"
check "Nginx 运行" \
  "ssh -o ConnectTimeout=5 $SERVER 'systemctl is-active nginx' 2>/dev/null | grep -q active"
check "Docker 运行" \
  "ssh -o ConnectTimeout=5 $SERVER 'systemctl is-active docker' 2>/dev/null | grep -q active"
check "WP 容器 Up" \
  "ssh $SERVER 'docker ps --filter name=bolent_wp --format {{.Status}}' 2>/dev/null | grep -q Up"
check "磁盘 < 80%" \
  "ssh $SERVER 'df -h / | tail -1' 2>/dev/null | awk '{print \$5}' | grep -vq '^8[0-9]%\|^9[0-9]%\|^100%'"

echo ""
echo "========================================="
echo "  通过: $PASS  |  失败: $FAIL"
echo "========================================="

if [ "$FAIL" -gt 0 ]; then
  echo "⚠️  存在失败项，请检查！"
  exit 1
else
  echo "✅ 所有检查通过"
fi
```

将此脚本保存为 `scripts/verify.sh` 并执行：

```bash
chmod +x scripts/verify.sh
bash scripts/verify.sh
```

---

## 附录

### A. 联系方式与资源

| 资源 | 地址 |
|------|------|
| GitHub 仓库 | `git@github.com:szpeter2026/szbolent-portal.git` |
| Gitee 仓库 | `git@gitee.com:szbenyx/szbolent-portal.git` |
| 关联仓库 | `looma-zervi`（Looma Python 后端） |
| 门户 IP | `47.115.168.107` |
| API 代理 IP | `api.genz.ltd` |
| Looma 后端 IP | `1.14.202.161` |
| 正式域名（备案后） | `www.szbolent.cn` / `www.szbolent.com.cn` |

### B. 常用 SSH 别名配置

在本地 `~/.ssh/config` 添加：

```
Host bolent-portal
    HostName 47.115.168.107
    User root
    IdentityFile ~/.ssh/id_ed25519

Host bolent-api-proxy
    HostName api.genz.ltd
    User root

Host bolent-looma
    HostName 1.14.202.161
    User root
```

然后可直接使用 `ssh bolent-portal` 等简化命令。

### C. 文档索引

| 文档 | 内容 |
|------|------|
| `docs/INTEGRATION.md` | WordPress / API / Tatha 联调清单 |
| `docs/LINEAGE.md` | 项目溯源与代码边界 |
| `docs/TENCENT_CLOUD_COMMERCE.md` | 腾讯云备案/支付/P0–P2 执行手册 |
| `docs/COMMERCE_ENTITY_DECISION.md` | 备案·支付·主体路径决策 |
| `docs/DUAL_REPO_WORK_GUIDE.md` | looma-zervi × szbolent-portal 双仓协作指引 |
| `docs/DUAL_REPO_SYNC_STATUS.md` | 双仓同步状态记录 |