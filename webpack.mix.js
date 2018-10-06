const {mix} = require('laravel-mix');

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

mix.webpackConfig({
  resolve: {
    alias: {
      // '@': path.resolve(__dirname, 'resources/assets/js')
    }
  },
  output: {
    publicPath: '/',
    chunkFilename: 'static/js/[name].[chunkhash].js'
  }
})
  .setPublicPath('public/')
  .js('resources/js/app.js', 'public/static/js')
  .sass('resources/css/app.scss', 'public/static/css')
  .version();
