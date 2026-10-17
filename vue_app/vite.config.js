import { VuetifyResolver } from 'unplugin-vue-components/resolvers';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';

const path = require("path");

// https://vitejs.dev/config/
export default defineConfig({
  build: {
    target: ['es2019', 'edge88', 'firefox78', 'chrome87', 'safari13.1'],
  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./tests/unit/setup.js'],
    include: ['tests/unit/**/*.spec.js'],
  },
  plugins: [vue(), Components({
      resolvers: [
        // Vuetify
        VuetifyResolver(),
      ],
    }),],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
      process: "process/browser",
      stream: "stream-browserify",
      zlib: "browserify-zlib",
      util: 'util'
    },
     extensions: ['.vue', '.js']
  },
})
