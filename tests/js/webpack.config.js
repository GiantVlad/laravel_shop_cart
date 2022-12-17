const path = require('path')
const { VueLoaderPlugin } = require('vue-loader');
const nodeExternals = require('webpack-node-externals')

module.exports = {
    //entry: './resources/assets/js/app.js',
    output: {
        path: path.resolve(__dirname, './tmp'),
        publicPath: '/tmp/',
        filename: 'build.js',
        devtoolModuleFilenameTemplate: '[absolute-resource-path]',
        devtoolFallbackModuleFilenameTemplate: '[absolute-resource-path]?[hash]'
    },
    devtool: 'inline-cheap-module-source-map',
    resolve: {
        // extensions: [ '.tsx', '.ts', '.js', '.vue' ],
        fallback: { "path": false, "constants": false, "stream": false },
        alias: {
            'vue$': 'vue/dist/vue.esm.js',
        }
    },
    mode: "development",
    plugins: [
        new VueLoaderPlugin()
    ],
    // externals: {
    //     fs: '{}',
    // },
    externals: [nodeExternals()],
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                exclude: /node_modules/
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.(png|jpg|gif|svg)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]?[hash]'
                }
            },
            {
                test: /\.scss$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'sass-loader'
                ]
            }
        ]
    }
}
