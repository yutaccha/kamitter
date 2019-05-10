const mix = require('laravel-mix')



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

mix.setPublicPath('kamitter/public')
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [
            require('postcss-css-variables')()
        ]
    })
    .version()

// mix.browserSync('kamitter.test')
// // //     .js('resources/js/app.js', 'public/js')
// // //     .sass('resources/sass/app.scss', 'public/css')
// // //     .options({
// // //         postCss: [
// // //             require('postcss-css-variables')()
// // //         ]
// // //     })
// // //     .version()