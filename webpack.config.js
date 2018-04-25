const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');
const exclude = /node_modules/;
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
                enforce: "pre",
                //exclude: exclude,
                use: [{
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ["@babel/preset-env", {
                                "targets": {
                                    "browsers": [
                                        "last 2 versions",
                                        "> 1%",
                                        "ie >= 11",
                                        "safari >= 11",
                                        "not dead",
                                        "Firefox ESR",
                                        "last 1 OperaMini versions"
                                    ]
                                }
                            }]
                        ]
                    }
                }]
            },
            {
                test: /\.vue$/,
                //exclude: exclude,
                use: [{loader: 'vue-loader'}]
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)(\?\S*)?$/,
                //exclude: exclude,
                use: [{
                    loader: 'file-loader',
                    query: {
                        outputPath: 'fonts/'
                    }
                }]
            },
            {
                test: /\.css$/,
                //exclude: exclude,
                use: [
                    MiniCssExtractPlugin.loader,
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
