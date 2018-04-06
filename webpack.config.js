var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/assets')
    .setPublicPath('/assets')
    .cleanupOutputBeforeBuild()

    .addEntry('backoffice', './assets/spa/backoffice/index.js')
    .addEntry('frontoffice', './assets/spa/frontoffice/index.js')
    .enableSassLoader(function(sassOptions) {}, {})
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning()
    .enableReactPreset()
    .configureBabel(function(babelConfig) {
        babelConfig.presets.push('react-app');
    })
;

module.exports = Encore.getWebpackConfig();