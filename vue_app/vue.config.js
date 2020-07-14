const path = require('path');

module.exports = {
  lintOnSave: false,
  "transpileDependencies": [
    "vuetify"
  ],
  configureWebpack: {
    resolve: {
      alias: {
        "@": path.resolve(__dirname, 'src/')
      }
    }
  }
}