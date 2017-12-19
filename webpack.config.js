var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('web/assets')
    .setPublicPath('/assets')
    .cleanupOutputBeforeBuild()

    .addEntry('backoffice', './app/Resources/spa/backoffice/index.js')
    .addEntry('frontoffice', './app/Resources/spa/frontoffice/index.js')
    .enableSassLoader(function(sassOptions) {}, {})
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning()
    .enableReactPreset()
    .configureBabel(function(babelConfig) {
        babelConfig.presets.push('react-app');
    })
;

module.exports = Encore.getWebpackConfig();