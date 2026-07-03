<template>
  <div class="main-layout">
    <Header />
    <main class="main-content">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<script setup lang="ts">
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
</script>

<style scoped lang="scss">
.main-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;

  .main-content {
    flex: 1;
    margin-top: 70px; // Header 高度
  }
}
</style>
