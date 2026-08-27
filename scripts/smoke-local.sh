#!/usr/bin/env bash
# =============================================
# 本地冒烟（开发机）
# Portal :3000 / Page Engine :5300 / WP :8800
# Looma：默认远端 http://api.genz.ltd（可用 LOOMA_SMOKE_BASE 覆盖为本地 :5200）
# 用法: bash scripts/smoke-local.sh
# =============================================
set -u

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
NC='\033[0m'

PASS=0
FAIL=0

# 现行拓扑：本地可不启 :5200，Looma 走公网 API
LOOMA_SMOKE_BASE="${LOOMA_SMOKE_BASE:-http://api.genz.ltd}"
LOOMA_SMOKE_BASE="${LOOMA_SMOKE_BASE%/}"

check_http() {
  local name="$1"
  local url="$2"
  local expect="${3:-200}"
  local code
  # curl 连不上时仍可能打印 000 且非零退出；勿再拼接 echo，避免变成 000000
  code=$(curl -sS -o /dev/null -w "%{http_code}" --connect-timeout 5 "$url" 2>/dev/null) || code="000"
  printf "  [%s] %s → %s ... " "$name" "$url" "$code"
  if [[ "$code" == "$expect" || ( "$expect" == "2xx" && "$code" =~ ^2[0-9][0-9]$ ) ]]; then
    echo -e "${GREEN}PASS${NC}"
    PASS=$((PASS + 1))
  elif [[ "$code" == "000" ]]; then
    echo -e "${RED}FAIL (unreachable)${NC}"
    FAIL=$((FAIL + 1))
  else
    # WP/Looma 部分端点未登录会 401/403，仍视为服务存活
    if [[ "$expect" == "up" && "$code" != "000" ]]; then
      echo -e "${GREEN}UP ($code)${NC}"
      PASS=$((PASS + 1))
    else
      echo -e "${YELLOW}WARN ($code, want $expect)${NC}"
      FAIL=$((FAIL + 1))
    fi
  fi
}

echo ""
echo "============================================"
echo "  szbolent-portal 本地冒烟"
echo "  $(date '+%Y-%m-%d %H:%M:%S')"
echo "  Looma base: ${LOOMA_SMOKE_BASE}"
echo "============================================"
echo ""

echo "=== Portal :3000 ==="
check_http "portal/" "http://127.0.0.1:3000/" "200"
check_http "poetry" "http://127.0.0.1:3000/poetry" "200"
check_http "blog" "http://127.0.0.1:3000/blog" "200"

echo ""
echo "=== Looma（${LOOMA_SMOKE_BASE}）==="
check_http "poetry/browse" "${LOOMA_SMOKE_BASE}/v1/poetry/browse?per_page=1" "200"
check_http "auth/profile" "${LOOMA_SMOKE_BASE}/v1/auth/profile" "up"
# 门户 Vite 代理到同一远端（与 vite.config /v1 → api.genz.ltd 对齐）
check_http "proxy poetry" "http://127.0.0.1:3000/v1/poetry/browse?page=1&per_page=1" "200"

echo ""
echo "=== Page Engine :5300 ==="
check_http "menus" "http://127.0.0.1:5300/v1/menus?product=szbolent" "up"
check_http "proxy menus" "http://127.0.0.1:3000/v1/menus?product=szbolent" "up"

echo ""
echo "=== WordPress :8800（本地真源，非生产 8080）==="
check_http "wp posts" "http://127.0.0.1:8800/wp-json/wp/v2/posts?per_page=1" "up"
check_http "proxy /wp-json" "http://127.0.0.1:3000/wp-json/wp/v2/posts?per_page=1" "up"

echo ""
echo "--------------------------------------------"
echo -e "  PASS=${GREEN}${PASS}${NC}  FAIL/WARN=${RED}${FAIL}${NC}"
echo "--------------------------------------------"

if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
exit 0
