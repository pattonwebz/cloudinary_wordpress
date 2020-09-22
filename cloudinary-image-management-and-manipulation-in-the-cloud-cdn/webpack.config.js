const Encore = require( '@symfony/webpack-encore' );

if ( ! Encore.isRuntimeEnvironmentConfigured() ) {
	Encore.configureRuntimeEnvironment( process.env.NODE_ENV || 'dev' );
}

// Encore is a small wrapper around Webpack, which makes Webpack configs easy.
// If further loaders and/or plugins are needed, refer to this page:
// https://symfony.com/doc/current/frontend/encore/custom-loaders-plugins.html

Encore.setOutputPath( 'assets/dist/' )
	.setPublicPath( '/assets/dist/' )

	.addEntry( 'cloudinary', './assets/js/main.js' )
	.addStyleEntry( 'video', './assets/css/video.scss')
	// Add more entries here if needed...

	.copyFiles({
		from: './assets/css/fonts',
		to: 'fonts/[path][name].[ext]'
	})
	.copyFiles({
		from: './assets/css',
		to: '[path][name].[hash:8].[ext]',
		pattern: /\.svg$/
	})

	.enableSingleRuntimeChunk()
	.cleanupOutputBeforeBuild()
	.enableBuildNotifications()
	.enableSourceMaps( ! Encore.isProduction() )

	// Enable plugins/loaders
	.enableSassLoader()
	.configureBabelPresetEnv( ( config ) => {
		config.useBuiltIns = 'usage';
		config.corejs = 3;
	} )
	.enableReactPreset()
	.disableImagesLoader()
	.addLoader( { test: /\.svg$/, loader: '@svgr/webpack' } );

module.exports = Encore.getWebpackConfig();
