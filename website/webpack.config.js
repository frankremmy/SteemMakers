// Based on https://github.com/microsoft/typescript-vue-starter#adding-webpack

var path = require('path')
var webpack = require('webpack')
const {VueLoaderPlugin} = require('vue-loader')

function createPlugins()
{
	var plugins = [];

	plugins.push(
		new webpack.DefinePlugin(
		{
			'process.env': {'NODE_ENV': JSON.stringify(process.env.NODE_ENV) }
		})
	);

	if (process.env.NODE_ENV === 'production')
	{
		plugins.push(
			new webpack.DefinePlugin(
			{
				SC_CALLBACK_URL: '"https://www.steemmakers.com/#/authentication"'
			})
		);
	}
	else
	{
		plugins.push(
			new webpack.DefinePlugin(
			{
				SC_CALLBACK_URL: '"http://app.localhost.com/#/authentication"'
			})
		);
	}
	plugins.push(
		new VueLoaderPlugin()	
	)

	return plugins;
}

module.exports = {
	mode: process.env.NODE_ENV,
	entry: './src/ts/main.ts',
	output:
	{
		path: path.resolve(__dirname, './dist'),
		publicPath: '/dist/',
		filename: 'build.js'
	},
	module:
	{
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader',
				options: {
                    esModule: true
                }
			},
			{
				test: /\.tsx?$/,
				loader: 'ts-loader',
				exclude: /node_modules/,
				options:
				{
					appendTsSuffixTo: [/\.vue$/],
				}
			},
			{
				test: /\.(png|jpg|gif|svg)$/,
				loader: 'file-loader',
				options:
				{
					name: '[name].[ext]?[hash]'
				}
			},
			{
				test: /\.css$/,
				use: ['style-loader', 'css-loader']
			}
		]
	},
	resolve:
	{
		extensions: ['.ts', '.js', '.vue', '.json'],
		alias:
		{
			'vue$': 'vue/dist/vue.esm.js'
		}
	},
	plugins: createPlugins(),
	devServer:
	{
		historyApiFallback: true,
		noInfo: true
	},
	performance:
	{
		hints: false
	},
	devtool: 'source-map'
}

// if (process.env.NODE_ENV === 'production')
// {
// 	module.exports.devtool = 'source-map'
// 	// http://vue-loader.vuejs.org/en/workflow/production.html
// 	module.exports.plugins = (module.exports.plugins || []).concat([
// 		new webpack.DefinePlugin({
// 			'process.env': {
// 			NODE_ENV: '"production"'
// 			}
// 		}),
// 		// new webpack.optimize.UglifyJsPlugin({
// 		// 	sourceMap: true,
// 		// 	compress: {
// 		// 	warnings: false
// 		// 	}
// 		// }),
// 		new webpack.LoaderOptionsPlugin({
// 			minimize: true
// 		})
// 	])
// }