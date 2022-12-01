import { VuetifyResolver } from 'unplugin-vue-components/resolvers';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import { createVuePlugin as vue } from 'vite-plugin-vue2';

const path = require("path");

// https://vitejs.dev/config/
export default defineConfig({
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