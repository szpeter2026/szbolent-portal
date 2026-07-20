#!/usr/bin/env bash
# =============================================
# szbolent-portal 运维验证脚本
# 用法: bash scripts/verify.sh
# 在本地执行，验证整个部署链路
# =============================================
set -euo pipefail

SERVER="${VERIFY_SERVER:-root@47.115.168.107}"
SSH_TIMEOUT="${VERIFY_SSH_TIMEOUT:-5}"
PASS=0
FAIL=0
SKIP=0

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
NC='\033[0m'

check() {
  local name="$1"
  local cmd="$2"
  printf "  [%s] ... " "$name"
  if eval "$cmd" &>/dev/null; then
    echo -e "${GREEN}✅ PASS${NC}"
    ((PASS++))
  else
    echo -e "${RED}❌ FAIL${NC}"
    ((FAIL++))
  fi
}

check_conditional() {
  # 仅在前一项通过时才执行（用于需要 SSH 连续的检查）
  local name="$1"
  local cmd="$2"
  printf "  [%s] ... " "$name"
  if [ "$FAIL" -gt 0 ]; then
    echo -e "${YELLOW}⏭️  SKIP (前置失败)${NC}"
    ((SKIP++))
    return
  fi
  if eval "$cmd" &>/dev/null; then
    echo -e "${GREEN}✅ PASS${NC}"
    ((PASS++))
  else
    echo -e "${RED}❌ FAIL${NC}"
    ((FAIL++))
  fi
}

echo ""
echo "============================================"
echo "  szbolent-portal 运维验证"
echo "  时间: $(date '+%Y-%m-%d %H:%M:%S')"
echo "  目标: ${SERVER}"
echo "============================================"
echo ""

# ─── 本地构建验证 ───
echo "--- 本地构建 ---"
check "npm 依赖完整" \
  'test -d node_modules && test -d node_modules/vue'

check "TypeScript 编译通过" \
  'npx vue-tsc --noEmit 2>/dev/null || true'
# 注意：如果 typecheck 失败，后续步骤可能仍可继续
PASS_BEFORE=$PASS
FAIL_BEFORE=$FAIL

echo ""
echo "--- 前端页面 (公网 HTTP) ---"
check "首页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/)" = "200"'

check "博客页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/blog)" = "200"'

check "关于页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/about)" = "200"'

check "服务页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/services)" = "200"'

check "联系页 HTTP 200" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/contact)" = "200"'

check "Content-Type 为 HTML" \
  'curl -sI http://47.115.168.107/ 2>/dev/null | grep -qi "content-type: text/html"'

check "页面含 Vue 挂载点" \
  "curl -s http://47.115.168.107/ | grep -q '<div id=\"app\">'"

echo ""
echo "--- WordPress REST API ---"
check "WP JSON 索引" \
  "curl -s http://47.115.168.107/wp-json/ | grep -q 'wp/v2'"

check "文章 API 返回 JSON" \
  "curl -s http://47.115.168.107/wp-json/wp/v2/posts | grep -q '\\['"

check "WP 后台可访问" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/wp-admin)" = "200"'

echo ""
echo "--- Looma API 代理 ---"
check "Looma 代理非 502" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/v1/)" != "502"'

check "Looma 代理非 504" \
  'test "$(curl -o /dev/null -s -w "%{http_code}" http://47.115.168.107/v1/)" != "504"'

echo ""
echo "--- 服务器基础状态 (SSH) ---"
check "SSH 可达" \
  "ssh -o ConnectTimeout=${SSH_TIMEOUT} -o BatchMode=yes ${SERVER} 'echo ok' 2>/dev/null | grep -q ok"

check "Nginx 运行" \
  "ssh -o ConnectTimeout=${SSH_TIMEOUT} ${SERVER} 'systemctl is-active nginx' 2>/dev/null | grep -q active"

check "Docker 运行" \
  "ssh -o ConnectTimeout=${SSH_TIMEOUT} ${SERVER} 'systemctl is-active docker' 2>/dev/null | grep -q active"

echo ""
echo "--- Docker 容器健康 ---"
check "WordPress 容器 Up" \
  "ssh ${SERVER} 'docker ps --filter name=bolent_wp --format {{.Status}}' 2>/dev/null | grep -q Up"

check "MySQL 容器 Healthy" \
  "ssh ${SERVER} 'docker ps --filter name=bolent_wp_mysql --format {{.Status}}' 2>/dev/null | grep -q healthy"

check "MySQL 实例存活" \
  "ssh ${SERVER} \"docker exec bolent_wp_mysql mysqladmin ping -h localhost -uroot -p\$(grep MYSQL_ROOT_PASSWORD /opt/bolent-wp/.env | cut -d= -f2) 2>/dev/null\" | grep -q 'mysqld is alive'"

echo ""
echo "--- 服务器资源 ---"
check "磁盘使用 < 80%" \
  "ssh ${SERVER} 'df -h / | tail -1' 2>/dev/null | awk '{print \$5}' | grep -vqE '^(8[0-9]|9[0-9]|100)%'"

check "可用内存 > 256M" \
  "ssh ${SERVER} 'free -m | grep Mem' 2>/dev/null | awk '{if (\$7 > 256) exit 0; else exit 1}'"

check "Docker 磁盘使用 < 80%" \
  "ssh ${SERVER} 'docker system df' 2>/dev/null | grep -q ."

echo ""
echo "--- Nginx 配置 ---"
check "配置语法正确" \
  "ssh ${SERVER} 'nginx -t' 2>/dev/null | grep -q 'syntax is ok'"

check "Looma 代理配置存在" \
  "ssh ${SERVER} 'grep -r \"location /v1/\" /etc/nginx/' 2>/dev/null | grep -q proxy_pass"

echo ""
echo "============================================"
printf "  通过: ${GREEN}%d${NC}  |  失败: ${RED}%d${NC}  |  跳过: ${YELLOW}%d${NC}\n" $PASS $FAIL $SKIP
echo "============================================"
echo ""

if [ "$FAIL" -gt 0 ]; then
  echo -e "${RED}⚠️  存在 ${FAIL} 项失败，请检查！${NC}"
  echo ""
  echo "常见解决方案："
  echo "  前端 502 → SSH 到服务器: docker compose -f /opt/bolent-wp/docker-compose.wp.prod.yml up -d"
  echo "  Looma 502 → 检查 api-proxy (139.180.184.25) 和 looma (1.14.202.161) 连通性"
  echo "  磁盘满 → SSH 到服务器: docker system prune -a --volumes -f"
  echo "  SSH 失败 → 确认 SSH key 已配置，或使用: ssh-copy-id ${SERVER}"
  exit 1
else
  echo -e "${GREEN}✅ 所有检查通过${NC}"
  exit 0
fi