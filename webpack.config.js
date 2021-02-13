const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = function (env, argv) {
    const isDevMode = argv.mode === 'development';

    return {
        devtool: isDevMode ? 'eval' : 'source-map',
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
                        options: {
                            outputPath: 'fonts/'
                        }
                    }]
                },
                {
                    test: /\.css$/,
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
                template: path.resolve(__dirname, "./assets/index.html"),
                xhtml: true,
                hash: true,
                minify: !isDevMode
            }),
            new MiniCssExtractPlugin({
                filename: "app.css"
            })
        ]
    };
};
