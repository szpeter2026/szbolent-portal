<template>
  <!--
    PermissionGuard — 权限门控组件

    用法：
      <PermissionGuard visibleTo="authenticated">
        <p>登录后才能看的内容</p>
      </PermissionGuard>

      <PermissionGuard
        visibleTo="employer"
        requiredRole="hr_senior"
        fallback="only-hr"
      >
        <template #default>
          <p>高级HR可见</p>
        </template>
        <template #only-hr>
          <p>您没有访问权限</p>
        </template>
      </PermissionGuard>

    Props:
      visibleTo     - 可见性级别（默认 'public'）
      requiredRole  - 指定角色
      requiredVC    - 指定 VC 凭证类型
      resource      - Casbin 资源路径（用于细粒度权限检查）
      action        - Casbin 操作（默认 'read'）
      showFallback  - 是否显示 fallback 内容（默认 true）
  -->

  <div v-if="isAuthorized" class="permission-guard">
    <slot />
  </div>
  <div v-else-if="showFallback">
    <slot name="fallback">
      <div class="permission-guard__fallback">
        <slot name="no-access">
          <p class="permission-guard__denied-text">
            {{ deniedMessage }}
          </p>
        </slot>
      </div>
    </slot>
  </div>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import { usePermission } from '@/composables/usePermission'
import type { VisibilityLevel, CasbinAction } from '@/api/page-engine.types'

const props = withDefaults(defineProps<{
  visibleTo?: VisibilityLevel
  requiredRole?: string | null
  requiredVC?: string | null
  resource?: string
  action?: CasbinAction
  showFallback?: boolean
  deniedMessage?: string
}>(), {
  visibleTo: 'public',
  requiredRole: null,
  requiredVC: null,
  resource: '',
  action: 'read',
  showFallback: true,
  deniedMessage: '您没有访问此内容的权限'
})

const { isVisible, canAccess, initialize } = usePermission()
const isAuthorized = ref(false)

// 权限检查
watchEffect(async () => {
  await initialize()

  // 1. 可见性检查
  const visible = isVisible(props.visibleTo, props.requiredRole, props.requiredVC)
  if (!visible) {
    isAuthorized.value = false
    return
  }

  // 2. Casbin 细粒度权限检查（仅当指定了 resource 时）
  if (props.resource) {
    const hasPermission = await canAccess(props.resource, props.action)
    isAuthorized.value = hasPermission
    return
  }

  isAuthorized.value = true
})
</script>

<style scoped>
.permission-guard__fallback {
  opacity: 0.7;
  padding: 1rem 0;
}

.permission-guard__denied-text {
  margin: 0;
  color: var(--color-text-secondary, #999);
  font-size: 0.875rem;
  font-style: italic;
}
</style>
