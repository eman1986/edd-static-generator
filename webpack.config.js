const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const VENDOR_LIBS = [
    'auth0-lock', 'axios', 'EventEmitter', 'lodash',
    'moment', 'save', 'sweetalert2', 'uuid',
    'vee-validate', 'vue', 'vue-router', 'vue-sweetalert2',
    'vuex'
];

module.exports = {
    entry: {
        bundle: './tsSrc/web.ts',
        vendor: VENDOR_LIBS
    },
    output: {
        path: path.join(__dirname, 'public'),
        filename: '[name].[chunkhash].min.js'
    },
    resolve: {
        extensions: ['.ts', '.js']
    },
    optimization: {
        runtimeChunk: true,
        splitChunks: {
            cacheGroups: {
                commons: {
                    chunks: "initial",
                    minChunks: 2,
                    maxInitialRequests: 5, // The default limit is too small to showcase the effect
                    minSize: 0 // This is example is too small to create commons chunks
                },
                vendor: {
                    test: /node_modules/,
                    chunks: "initial",
                    name: "vendor",
                    priority: 10,
                    enforce: true
                }
            }
        }
    },
    module: {
        rules: [
            {
                use: 'babel-loader',
                test: /\.js$/,
                exclude: /node_modules/
            },
            {
                test: /\.css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader'
                ]
            },
            {
                test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                use: 'file-loader?name=fonts/[name].[ext]'
            },
            {
                test: /\.ts?$/,
                use: 'ts-loader',
                exclude: /node_modules/
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].css",
            chunkFilename: "[id].css"
        }),
        // new webpack.optimize.CommonsChunkPlugin({
        //     names: ['vendor', 'manifest']
        // }),
        new UglifyJsPlugin(),
        new HtmlWebpackPlugin({
            title: 'HtmlWebpackPlugin example',
            favicon: 'favicon.ico',
            filename: 'templates/shared/master.twig'
        })
    ]
};