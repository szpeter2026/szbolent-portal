<template>
  <div class="poetry-layout">
    <router-view />
    <!-- 诗词 AI 对话（position:fixed，全局浮层） -->
    <ChatDialog />
  </div>
</template>

<script setup lang="ts">
import ChatDialog from '@/components/shared/ChatDialog.vue'
</script>

<style scoped>
.poetry-layout {
  min-height: 100vh;
}
</style>
