var path = require('path')
var { VueLoaderPlugin } = require('vue-loader');

module.exports = {
    //entry: './resources/assets/js/app.js',
    output: {
        path: path.resolve(__dirname, './tmp'),
        publicPath: '/tmp/',
        filename: 'build.js'
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js',
        }
    },
    mode: "development",
    plugins: [
        new VueLoaderPlugin()
    ],
    externals: {
        fs: '{}'
    },
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
