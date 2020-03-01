let mix = require('laravel-mix');

const vendors = [
  'vue',
  'vue-router',
  'vuex',
  'bootstrap',
  'tether',
  'popper.js',
  'jquery',
  'lodash',
  'axios',
];

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js('resources/js/app.js', 'public/js/app.js')
  .extract(vendors)
  .sass('resources/sass/app.scss', 'public/css');

var webpackConfig = {};
if (mix.config.production) {
  mix.version();
}else{
  /*webpackConfig = {
    rules: [
      {
        enforce: 'pre',
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        exclude: /node_modules/
      }
    ]
  };*/

  mix.sourceMaps();
}

mix.options({
  uglify: {
    uglifyOptions: {
      parallel: 4,
      keep_fnames: true
    }
  }
});

mix.webpackConfig({
  watchOptions: {
    aggregateTimeout: 300,
    poll: 1000
  }
});