let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Framework Configuration
 |--------------------------------------------------------------------------
 |
 | Themosis framework configuration. Let's expose the Themosis core
 | object to users so they can register custom fields and other utilities
 | for their project in their own scripts.
 |
 */
mix.webpackConfig(webpack => {
    return {
        output: {
            library: 'Themosis',
            libraryExport: 'default'
        },
        resolve: {
            extensions: [".ts", ".tsx", ".js", ".json"]
        },
        module: {
            rules: [
                {
                    test: /\.tsx?$/,
                    loader: "ts-loader",
                    exclude: /node_modules/
                },
                {
                    test: /\.scss$/,
                    loader: "sass-loader"
                }
            ]
        },
        externals: {
            jquery: 'jQuery',
            lodash: 'lodash',
            'lodash-es': 'lodash'
        }
    };
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your application. By default, we are compiling the Sass
 | file, as well as bundling up your JS files.
 |
 */

mix.react('resources/assets/js/index.ts', 'dist/js/themosis.core.js');
mix.js('resources/assets/js/deprecated/poststatus.js', 'dist/js/themosis.poststatus.js');