const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');
const isDevMode = process.env.NODE_ENV !== 'production';

module.exports = {
    devtool: 'source-map',
    entry: path.resolve(__dirname + '/assets/app.js'),
    output: {
        publicPath: '/build/',
        filename: "app.js",
        path: path.resolve(__dirname, "./public/build")
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                use: [{
                    loader: 'babel-loader'
                }]
            },
            {
                test: /\.vue$/,
                use: [{
                    loader: 'vue-loader'
                }]
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)(\?\S*)?$/,
                use: [{
                    loader: 'file-loader',
                    query: {
                        outputPath: 'fonts/'
                    }
                }]
            },
            {
                test: /\.css$/,
                use: [
                    isDevMode ? 'vue-style-loader' : MiniCssExtractPlugin.loader,
                    'css-loader'
                ]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, "./public/index.html"),
            template: path.resolve(__dirname, "./assets/index.html")
        }),
        new MiniCssExtractPlugin({
            filename: "app.css?[hash]"
        })
    ]
};
