const Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured directly by the
// process.env.NODE_ENV environment variable.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")
    // public path used by the web server to access the output path
    .setPublicPath("/build")

    // L'entrée principale
    .addEntry("app", "./assets/app.js")

    // Active le bridge Stimulus (essentiel pour votre scroll infini)
    .enableStimulusBridge("./assets/controllers.json")

    // Split files into smaller chunks
    .splitEntryChunks()

    // --- LA LIGNE MANQUANTE EST ICI ---
    .enableSingleRuntimeChunk()
    // ----------------------------------

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = 3;
    });

module.exports = Encore.getWebpackConfig();