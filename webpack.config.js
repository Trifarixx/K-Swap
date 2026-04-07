const Encore = require("@symfony/webpack-encore");

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore.setOutputPath("public/build/")
    .setPublicPath("/build")
    .addEntry("app", "./assets/app.js")
    .enableStimulusBridge("./assets/controllers.json")
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .enableSassLoader()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

const config = Encore.getWebpackConfig();

// Remove babel-loader for JS files
config.module.rules = config.module.rules.filter((rule) => {
    if (rule.test && rule.test.toString().includes("js")) {
        return false;
    }
    return true;
});

// Add JS rule that handles modules without babel processing
config.module.rules.push({
    test: /\.js$/,
    type: "javascript/auto",
    exclude: /node_modules/,
});

module.exports = config;
