import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
  build: {
    target: 'baseline-widely-available',
    rollupOptions: {
      output: {
        onlyExplicitManualChunks: true,
        manualChunks(id) {
          if (!id.includes('/node_modules/')) return
          if (id.includes('/apexcharts/dist/core')) return 'chart-engine'
          if (id.includes('/@sentry/')) return 'sentry'
          if (id.includes('/vuetify/')) return 'vuetify'
          if (/\/(?:@vue|vue|vue-router|vuex)\//.test(id)) return 'vue'
        },
      },
    },
  },
  test: {
    server: { deps: { inline: ['vuetify'] } },
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./tests/unit/setup.js'],
    include: ['tests/unit/**/*.spec.js'],
  },
  plugins: [vue(), vuetify({ autoImport: true })],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      process: 'process/browser',
    },
    extensions: ['.vue', '.js', '.mjs', '.json'],
  },
})
