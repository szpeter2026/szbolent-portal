#!/usr/bin/env bash
# upload-aliyun.sh — 阿里内容站产物上传（本地构建 → 同源化 → ssh 上传 → 远端验证）
#
# 背景（2026-08-27，阿里 API 指向迁移后）：
#   阿里云 47.115.168.107 = 内容终端（非 Looma 大脑）。站点 /v1/ 由 nginx 反代指天翼 14.29.216.219。
#   因此上传产物必须满足「同源」：dist 内不得残留 api.genz.ltd（否则跨域 / 直连海外 / 指向漂移）。
#
# 资产清单（钉死）：
#   [有源] szbolent-portal dist   本地 $ROOT/dist          → 阿里 /var/www/szbolent-portal/dist
#   [无源] poetries-h5             仅阿里 /var/www/poetries-h5/dist，本地源缺失 → pull-poetries 拉回基线
#   [无源] WP 主题 bolent-astra-child 仅阿里（compose 挂载 ./themes/bolent-astra-child，本地仓无 themes/）→ 待重建
#   [参考] nginx conf              阿里 live=/etc/nginx/conf.d/{00-szbolent,szbolent-h5}.conf；
#                                  本地仓内参考文件非 live，勿上传 → pull-nginx 拉回备份
#
# 用法：
#   ./upload-aliyun.sh               # 默认：构建 → 同源化 → 上传 portal dist → 远端验证
#   ./upload-aliyun.sh --no-build    # 跳过 npm run build（dist 已是最新产物）
#   ./upload-aliyun.sh --dry-run     # 只做本地构建 + 同源化 + 检查，不 ssh 不写远端
#   ./upload-aliyun.sh pull-poetries # 阿里 poetries-h5 dist → 本地备份基线（源缺失的兜底）
#   ./upload-aliyun.sh pull-nginx    # 阿里 live nginx conf → 本地备份（防施工/回滚依据）
#
# SSH（第 13 步勘察用 root）：
#   未配免密时先 ssh-copy-id root@47.115.168.107 一次；
#   或指定密钥： SSH_KEY=~/path/key.pem ./upload-aliyun.sh
#
# 缓存提醒：dist chunk 带 hash，且阿里 nginx 对 .js|.css 配 immutable cache。
#   若构建后 chunk 文件名未变但内容变了，用户端会吃旧缓存（需强刷或改版本后缀）。
set -euo pipefail

# ---------- 配置 ----------
ALI_HOST="${ALI_HOST:-47.115.168.107}"
ALI_USER="${ALI_USER:-root}"
REMOTE_BASE=/var/www
REMOTE_PORTAL_DIST="${REMOTE_BASE}/szbolent-portal/dist"
REMOTE_POETRIES_DIST="${REMOTE_BASE}/poetries-h5/dist"
REMOTE_NGINX_CONF_DIR=/etc/nginx/conf.d

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PORTAL_DIST="$ROOT/dist"
BACKUP_DIR="$ROOT/backup/aliyun"

# macOS 兼容的 BSD sed
if [[ "$(uname)" == "Darwin" ]]; then
  SED_CMD=(sed -i '')
else
  SED_CMD=(sed -i)
fi

MODE="upload"   # upload | pull-poetries | pull-nginx
DO_BUILD=1
DRY_RUN=0

for arg in "$@"; do
  case "$arg" in
    --no-build) DO_BUILD=0 ;;
    --dry-run)  DRY_RUN=1 ;;
    pull-poetries) MODE="pull-poetries" ;;
    pull-nginx)    MODE="pull-nginx" ;;
    -h|--help) grep '^#' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
    *) echo "未知参数: $arg" >&2; exit 1 ;;
  esac
done

red()  { printf '\033[0;31m%s\033[0m\n' "$*"; }
green(){ printf '\033[0;32m%s\033[0m\n' "$*"; }
warn() { printf '\033[0;33m%s\033[0m\n' "$*"; }
err()  { red "✗ $*" >&2; exit 1; }

# ---------- SSH ----------
SSH_TARGET="${ALI_USER}@${ALI_HOST}"
SSH_OPTS=(-o ConnectTimeout=10 -o ServerAliveInterval=30)
[ -n "${SSH_KEY:-}" ] && SSH_OPTS+=(-i "$SSH_KEY" -o IdentitiesOnly=yes)

ssh_aliyun() { ssh "${SSH_OPTS[@]}" "$SSH_TARGET" "$@"; }

preflight() {
  if [ "$DRY_RUN" -eq 1 ]; then
    green "[dry-run] 跳过 SSH 预检"
    return
  fi
  green "SSH 预检 → ${SSH_TARGET}"
  ssh_aliyun "echo ok && uname -srm" || err "SSH 失败。先 ssh-copy-id ${SSH_TARGET},或设 SSH_KEY=..."
}

# ---------- 同源化（幂等闸门） ----------
same_origin() {
  local dist="$1"
  [ -d "$dist" ] || err "产物目录不存在: $dist"
  # 残留检测（js/css/html 都查）
  if grep -rl 'api\.genz\.ltd' "$dist" --include='*.js' --include='*.css' --include='*.html' 2>/dev/null | grep -qv '\.bak'; then
    warn "发现 api.genz.ltd 残留 → 本地同源化（http/https 一并清空，保留引号成同源空串）"
    while IFS= read -r f; do
      [ -f "$f" ] || continue
      case "$f" in *.bak) continue;; esac
      # 两条显式表达式（BSD sed BRE 不支持 \?，勿合并）
      "${SED_CMD[@]}" -e 's#http://api\.genz\.ltd##g' -e 's#https://api\.genz\.ltd##g' "$f"
    done < <(grep -rl 'api\.genz\.ltd' "$dist" --include='*.js' --include='*.css' --include='*.html' 2>/dev/null || true)
  fi
  local remain
  remain="$(grep -rl 'api\.genz\.ltd' "$dist" --include='*.js' --include='*.css' --include='*.html' 2>/dev/null | grep -v '\.bak' || true)"
  if [ -n "$remain" ]; then
    red "✗ 同源化后仍有残留:"
    echo "$remain" >&2
    exit 1
  fi
  green "✓ 同源闸门通过：dist 内 api.genz.ltd 残留 = 0"
}

# ---------- 上传 portal dist ----------
upload_portal() {
  [ -d "$PORTAL_DIST" ] || err "无产物目录，先构建: (cd $ROOT && npm run build)"
  if [ "$DO_BUILD" -eq 1 ]; then
    green "构建 szbolent-portal ..."
    ( cd "$ROOT" && npm run build )
  fi
  same_origin "$PORTAL_DIST"
  if [ "$DRY_RUN" -eq 1 ]; then
    green "[dry-run] 将 rsync → ${SSH_TARGET}:${REMOTE_PORTAL_DIST}/"
    return
  fi
  green "上传 → ${SSH_TARGET}:${REMOTE_PORTAL_DIST}/"
  ssh_aliyun "mkdir -p ${REMOTE_PORTAL_DIST}"
  rsync -avz -e "ssh ${SSH_OPTS[*]}" --delete \
    "${PORTAL_DIST}/" "${SSH_TARGET}:${REMOTE_PORTAL_DIST}/"
  green "✓ 上传完成，远端同源复核 ..."
  local bad
  bad="$(ssh_aliyun "grep -rl 'api\\.genz\\.ltd' ${REMOTE_PORTAL_DIST}/assets --include='*.js' 2>/dev/null || true")"
  if [ -n "$bad" ]; then
    red "✗ 远端仍残留 api.genz.ltd: $bad"
    exit 1
  fi
  green "✓ 远端同源复核通过（残留=0）"
  # 缓存提示：对比本地/远端 index.html 引用的 chunk 名
  local lc rc
  lc="$(grep -oE '[A-Za-z0-9_-]+-[A-Za-z0-9_-]{8}\.js' "$PORTAL_DIST/index.html" | sort -u | tr '\n' ' ')"
  rc="$(ssh_aliyun "grep -oE '[A-Za-z0-9_-]+-[A-Za-z0-9_-]{8}\.js' ${REMOTE_PORTAL_DIST}/index.html | sort -u | tr '\n' ' '" 2>/dev/null || true)"
  if [ "$lc" = "$rc" ]; then
    warn "⚠ chunk 文件名与线上相同（${lc}），nginx immutable cache 会命中旧缓存 → 浏览器需强刷 Cmd/Ctrl+Shift+R"
  fi
}

# ---------- 拉回 poetries-h5 基线（源缺失兜底） ----------
pull_poetries() {
  [ "$DRY_RUN" -eq 1 ] && { green "[dry-run] 将拉取 ${SSH_TARGET}:${REMOTE_POETRIES_DIST} → $BACKUP_DIR/poetries-h5-dist/"; return; }
  green "拉回 poetries-h5 基线 → $BACKUP_DIR/poetries-h5-dist/"
  ssh_aliyun "mkdir -p ${REMOTE_POETRIES_DIST}"
  mkdir -p "$BACKUP_DIR"
  rsync -avz -e "ssh ${SSH_OPTS[*]}" "${SSH_TARGET}:${REMOTE_POETRIES_DIST}/" "$BACKUP_DIR/poetries-h5-dist/"
  green "✓ 已拉回。提示：poetries-h5 本地构建源缺失，此为唯一基线，请勿删除。"
}

# ---------- 拉回 live nginx conf 备份 ----------
pull_nginx() {
  [ "$DRY_RUN" -eq 1 ] && { green "[dry-run] 将拉取 ${SSH_TARGET}:${REMOTE_NGINX_CONF_DIR}/{00-szbolent,szbolent-h5}.conf → $BACKUP_DIR/nginx-conf/"; return; }
  green "拉回 live nginx conf → $BACKUP_DIR/nginx-conf/"
  mkdir -p "$BACKUP_DIR/nginx-conf"
  for f in 00-szbolent.conf szbolent-h5.conf; do
    ssh_aliyun "test -f ${REMOTE_NGINX_CONF_DIR}/$f" && \
      scp "${SSH_OPTS[@]}" "${SSH_TARGET}:${REMOTE_NGINX_CONF_DIR}/$f" "$BACKUP_DIR/nginx-conf/" || \
      warn "阿里无 $f（跳过）"
  done
  green "✓ 已拉回。注意：本仓 nginx/ 参考文件非 live，勿反向上传。"
}

# ---------- 入口 ----------
case "$MODE" in
  pull-poetries) preflight; pull_poetries ;;
  pull-nginx)    preflight; pull_nginx ;;
  upload)        preflight; upload_portal ;;
esac
green "完成（mode=${MODE} dry_run=${DRY_RUN}）"
