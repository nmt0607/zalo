const mix = require("laravel-mix");
const path = require("path");

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

mix.js("resources/js/index.js", "public/js")
    .react()
    .extract(["react", "react-dom", "react-router-dom"]);

mix.webpackConfig({
    resolve: {
        alias: {
            api: path.resolve("resources/js/api"),
            components: path.resolve("resources/js/components"),
            pages: path.resolve("resources/js/pages"),
            define: path.resolve("resources/js/define"),
            static: path.resolve("resources/static"),
        },
    },
});

