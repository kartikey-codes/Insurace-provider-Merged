let mix = require("laravel-mix");
let path = require("path");

/**
 * Currently using Laravel-Mix to compile front end assets
 * @todo Switch to Vite
 */

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 | https://github.com/JeffreyWay/laravel-mix
 |--------------------------------------------------------------------------
 */

const vueConfig = {
	version: 2,
	runtimeOnly: true,
	options: {
		compilerOptions: {
			compatConfig: {
				MODE: 2,
			},
		},
	},
};

proxy_port = "8765";
if (process.env.REVKEEP_WEBPACK_PROXY_PORT) {
	proxy_port = process.env.REVKEEP_WEBPACK_PROXY_PORT;
}

proxy_host = "localhost";
if (process.env.REVKEEP_WEBPACK_PROXY_HOST) {
	proxy_host = process.env.REVKEEP_WEBPACK_PROXY_HOST;
}

mix.setPublicPath("webroot/")
	// Pre-Authentication Area
	.js("assets/js/login.js", "js")
	// Authenticated Areas (SPAs)
	.js("assets/js/clients.js", "js")
	.vue(vueConfig)
	.js("assets/js/vendors.js", "js")
	.vue(vueConfig)
	.js("assets/js/admins.js", "js")
	.vue(vueConfig)
	// Area SASS -> CSS
	.sass("assets/sass/login.scss", "css")
	.sass("assets/sass/clients.scss", "css")
	.sass("assets/sass/vendors.scss", "css")
	.sass("assets/sass/admins.scss", "css")
	// Helper SASS -> CSS
	.sass("assets/sass/error.scss", "css")
	.sass("assets/sass/pdf.scss", "css")
	.options({
		processCssUrls: false,
	})
	.webpackConfig({
		resolve: {
			alias: {
				vue$: path.resolve(__dirname, "node_modules/vue/dist/vue.esm.js"),
				"@": path.resolve(__dirname, "assets", "js"),
			},
		},
		watchOptions: {
			ignored: [
				".devcontainer/**",
				".idea/**",
				".vscode/**",
				".git/**",
				"bin/**",
				"config/**",
				"docker/**",
				"logs/**",
				"node_modules/**",
				"plugins/**",
				"storage/**",
				"tests/**",
				"tmp/**",
				"vendor/**",
				"webroot/**",
				".artifactignore",
				".buildpath",
				".dockerignore",
				".editorconfig",
				".gitattributes",
				".gitignore",
				".htaccess",
				".phpunit.result.cache",
				".prettierrc",
				"phpcs.xml",
				"phpstan.neon",
				"phpunit.xml.dist",
				"postCreateCommand.sh",
				"PROBLEMS.md",
				"README.md",
			],
		},
	})
	.disableNotifications()
	.sourceMaps(true)
	.extract()
	.browserSync({
		reloadDelay: 100,
		awaitWriteFinish: true,
		notify: false,
		files: ["templates/**/*", "assets/**/*"],
		// CakePHP Development Server
		proxy: `${proxy_host}:${proxy_port}`,
	})
	// Minify JS in production build
	.minify("webroot/js/vendor.js") // 3rd-party node_modules code
	.minify("webroot/js/admins.js")
	.minify("webroot/js/login.js")
	.minify("webroot/js/clients.js")
	.minify("webroot/js/vendors.js")
	// Minify CSS in production build
	.minify("webroot/css/admins.css")
	.minify("webroot/css/login.css")
	.minify("webroot/css/clients.css")
	.minify("webroot/css/vendors.css")
	// Version assets in manifest file
	.version();

module.exports = {
	target: "node",
};
