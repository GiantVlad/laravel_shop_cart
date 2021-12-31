let mix = require('laravel-mix');

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
            'bootstrap-confirmation': 'bootstrap-confirmation2/bootstrap-confirmation.js'  // relative to node_modules
        },
        //extensions: [ '.tsx', '.ts', '.js', '.vue' ],
    }
});
mix.js('resources/assets/js/app.js', 'public/js').vue()
   .sass('resources/assets/sass/app.scss', 'public/css');
