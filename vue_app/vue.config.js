const path = require('path')

module.exports = {
  lintOnSave: false,
  transpileDependencies: ['vuetify'],
  devServer: {
    progress: false,
  },
  configureWebpack: {
    devtool: 'source-map',
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'src/'),
      },
    },
    /*optimization: {
            splitChunks: {
                minSize: 10000,
                maxSize: 250000,
            },
        },*/
    /*loader: {
            test: /\.(eot|svg|ttf|woff|woff2)$/,
            loader: 'file-loader?name=[name].[ext]'
        },*/
  },
}
