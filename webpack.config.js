const ExtractTextPlugin = require('extract-text-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const webpack = require('webpack');
const path = require('path');
const exclude = /node_modules/;

module.exports = {
    devtool: '#source-map',
    entry: path.resolve(__dirname + '/assets/app.js'),
    output: {
        publicPath: '/build/',
        filename: "app.[hash].js",
        path: path.resolve(__dirname, "./public/build")
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                enforce: "pre",
                exclude: exclude,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                ["@babel/preset-env", {
                                    "targets": {
                                        "browsers": ["last 1 versions"]
                                    }
                                }]
                            ]
                        }
                    }
                ]
            },
            {
                test: /\.vue$/,
                exclude: exclude,
                use: ['vue-loader']
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)(\?\S*)?$/,
                loader: 'file-loader',
                query: {
                    outputPath: 'fonts/'
                }
            },

            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['css-loader'],
                })
            },
            {
                test: /\.s[ac]ss$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: [
                        {
                            loader: "css-loader",
                            options: {
                                importLoaders:  1,
                                sourceMap: true
                            }
                        }
                    ],
                })
            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, "./public/index.html"),
            template: path.resolve(__dirname, "./assets/index.html")
        }),
        new webpack.optimize.ModuleConcatenationPlugin(),
        new ExtractTextPlugin({
            filename: "app.[hash].css",
            allChunks: true
        }),
        // оптимизация для продакшена
        /*
        new webpack.DefinePlugin({
            'process.env': {
                'NODE_ENV': JSON.stringify('production')
            }
        }),
        new webpack.optimize.AggressiveMergingPlugin(),
        new webpack.optimize.OccurrenceOrderPlugin(),
        new webpack.LoaderOptionsPlugin({
            minimize: true
        }),
        new UglifyJSPlugin({
            sourceMap: true,
            compress: {
                warnings: false
            }
        })
        */
    ]
};
