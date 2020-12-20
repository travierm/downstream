const path = require('path');

module.exports = {
  lintOnSave: false,
  "transpileDependencies": [
    "vuetify"
  ],
  configureWebpack: {
    devtool: 'source-map',
    resolve: {
      alias: {
        "@": path.resolve(__dirname, 'src/')
      }
    }
  }
}