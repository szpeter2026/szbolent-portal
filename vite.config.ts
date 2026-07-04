/// <reference types="vitest/config" />
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';

// https://vitejs.dev/config/
import path from 'node:path';
import { storybookTest } from '@storybook/addon-vitest/vitest-plugin';
import { playwright } from '@vitest/browser-playwright';
const dirname = typeof __dirname !== 'undefined' ? __dirname : path.dirname(fileURLToPath(import.meta.url));

// More info at: https://storybook.js.org/docs/next/writing-tests/integrations/vitest-addon
export default defineConfig({
  plugins: [vue(),
  // 自动导入 Vue 相关函数
  AutoImport({
    imports: ['vue', 'vue-router', 'pinia'],
    dts: 'src/auto-imports.d.ts'
  }),
  // 自动导入组件
  Components({
    dts: 'src/components.d.ts'
  })],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    port: 3000,
    host: '0.0.0.0',
    open: true,
    proxy: {
      // Looma REST（本地联调 · 见 docs/TENCENT_CLOUD_COMMERCE.md §6.1）
      '/v1': {
        target: 'http://127.0.0.1:5200',
        changeOrigin: true
      },
      // Legacy bolent backend（过渡）
      '/api': {
        target: 'http://localhost:8001',
        changeOrigin: true
      },
      // 代理 WordPress REST API (PoetImmortal 博客)
      '/wp-json': {
        target: process.env.VITE_WP_PROXY || 'http://localhost:8800',
        changeOrigin: true,
        rewrite: path => path
      }
    }
  },
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: false,
    minify: 'esbuild',
    chunkSizeWarningLimit: 1000
  },
  test: {
    projects: [{
      extends: true,
      plugins: [
      // The plugin will run tests for the stories defined in your Storybook config
      // See options at: https://storybook.js.org/docs/next/writing-tests/integrations/vitest-addon#storybooktest
      storybookTest({
        configDir: path.join(dirname, '.storybook')
      })],
      test: {
        name: 'storybook',
        browser: {
          enabled: true,
          headless: true,
          provider: playwright({}),
          instances: [{
            browser: 'chromium'
          }]
        }
      }
    }]
  }
});