const mix = require('laravel-mix')

mix.setPublicPath('.')

mix.webpackConfig({
    module: {
        rules: [
            { // Allow .scss files imported glob
                test: /\.scss/,
                loader: 'import-glob-loader'
            }
        ]
    }
})

mix.browserSync('kamitter.test')
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [
            require('postcss-css-variables')()
        ]
    })
    .version()