const ExtractTextPlugin = require('extract-text-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const path = require('path');
const exclude = /node_modules/;

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
                exclude: exclude,
                use: [{
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ["@babel/preset-env", {
                                "targets": {
                                    "browsers": ["last 2 versions"]
                                }
                            }]
                        ]
                    }
                }]
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
                test: /\.css$|\.s[ac]ss$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: [{
                        loader: "css-loader",
                        options: {
                            minimize: true,
                            sourceMap: true
                        }
                    }],
                })
            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            filename: path.resolve(__dirname, "./public/index.html"),
            template: path.resolve(__dirname, "./assets/index.html")
        }),
        new ExtractTextPlugin({
            filename: "app.css?[hash]"
        })
    ]
};
