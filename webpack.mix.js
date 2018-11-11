let mix = require('laravel-mix');

const vendors = [
  'vue',
  'vue-router',
  'vuex',
  'bootstrap',
  'tether',
  'popper.js',
  'jquery',
  'lodash'
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

if (mix.inProduction()) {
  mix.version();
}else{
  mix.sourceMaps();
}

mix.options({
});

mix.webpackConfig({
  plugins: [
    //new BundleAnalyzerPlugin()
  ],
  watchOptions: {
    aggregateTimeout: 300,
    poll: 1000
  }
});