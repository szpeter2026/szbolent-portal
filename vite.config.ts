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
  base: process.env.VITE_BASE_PATH || '/',
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
      // =============================================
      // Page Engine :5300 — 动态菜单 / 页面 Schema / 权限
      // =============================================
      '/v1/menus': {
        target: 'http://127.0.0.1:5300',
        changeOrigin: true,
      },
      '/v1/pages': {
        target: 'http://127.0.0.1:5300',
        changeOrigin: true,
      },
      '/v1/permissions': {
        target: 'http://127.0.0.1:5300',
        changeOrigin: true,
      },

      // =============================================
      // Looma — 诗词 / RAG / Auth / 支付 等
      // 本地可不启 :5200，默认回源 api.genz.ltd
      // =============================================
      '/v1': {
        target: process.env.VITE_LOOMA_PROXY || 'http://api.genz.ltd',
        changeOrigin: true,
      },

      // 代理 WordPress REST API (PoetImmortal 博客)
      '/wp-json': {
        target: process.env.VITE_WP_PROXY || 'http://localhost:8800',
        changeOrigin: true,
        rewrite: path => path,
      },
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
