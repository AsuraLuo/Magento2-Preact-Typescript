import * as path from 'path'
import * as webpack from 'webpack'
import * as os from 'os'
import { WebpackConfig, InputConfig } from '../packages'
import { themeConfig } from '../build'
import compileModules from './modules'
const readJsonSync = require('read-json-sync')
const HappyPack = require('happypack')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const ProgressBarPlugin = require('progress-bar-webpack-plugin')
const HardSourceWebpackPlugin = require('hard-source-webpack-plugin')
const FirendlyErrorePlugin = require('friendly-errors-webpack-plugin')
const ParallelUglifyPlugin = require('webpack-parallel-uglify-plugin')
const LodashModulePlugin = require('lodash-webpack-plugin')
const SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin')
const WebpackPwaManifest = require('webpack-pwa-manifest')
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin
const happyThreadPool = HappyPack.ThreadPool({ size: os.cpus().length })

const { area, src, mode } = themeConfig.default
const createEntry: any = ((list: string[]) => {
    const params: any = {}
    list.map((url: string) => {    
        params[url] = path.resolve(__dirname, `../app/src/pager/${url}.tsx`)
    })

    return new Promise((resolve) => resolve(params))
})
const createHappyPlugin: any = (id: string, loaders: string[]) => new HappyPack({
    id: id,
    loaders: loaders,
    threadPool: happyThreadPool,
    verboseWhenProfiling: true
})
const dllJson: any = readJsonSync(`../app/design/${area}/${src}/web/dll/vendor.dll.manifest.json`)

const baseConfig = new WebpackConfig({
    root:  path.join(__dirname, '../app'),
    entry: () => createEntry([
        ...compileModules
    ]),
    output: {
        path: path.join(__dirname, `../../app/design/${area}/${src}/web/js`),
        filename: '[name].bundle.js',
        publicPath: path.resolve(__dirname, `../../app/design/${area}/${src}/web/js`)
    },
    mode: mode,
    devtool: 'inline-source-map',
    performance: {
        hints: 'warning',
        maxAssetSize: 50000000,
        maxEntrypointSize: 30000000
    },
    plugins: [
        new webpack.DllReferencePlugin({
            context: __dirname,
            manifest: { 
                content: dllJson.content, 
                name: dllJson.name 
            }
        }),
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify(mode),
        }),
        new FirendlyErrorePlugin(),
        // new BundleAnalyzerPlugin({
        //     analyzerMode: 'server',
        //     analyzerHost: '127.0.0.1',
        //     analyzerPort: 8889,
        //     reportFilename: 'report.html',
        //     defaultSizes: 'parsed',
        //     openAnalyzer: true,
        //     generateStatsFile: false,
        //     statsFilename: 'stats.json',
        //     statsOptions: null,
        //     logLevel: 'info',
        // }),
        new ProgressBarPlugin({
            entries: true,
            modules: true,
            modulesCount: 100,
            profile: true
        }),
        new LodashModulePlugin({
            collections: true,
            paths: true
        }),
        createHappyPlugin('happy-babel', [{
            loader: 'babel-loader',
            options: {
                babelrc: true,
                cacheDirectory: true,
                cacheCompression: true
            }
        }]),
        new MiniCssExtractPlugin({
            filename: `[name].[contenthash:8].css`
        }),
        new webpack.optimize.ModuleConcatenationPlugin(),
        new webpack.NoEmitOnErrorsPlugin(),
        new HardSourceWebpackPlugin(),
        new SWPrecacheWebpackPlugin({
            cacheId: 'preact-app',
            filename: 'sw.js',
            staticFileGlobs: [
                path.join(__dirname, `../../app/design/${area}/${src}/web/**/*.{js,html,css}`)
            ],
            minify: true,
            stripPrefix: path.join(__dirname, `../../app/design/${area}/${src}/web/`)
        }),
        new WebpackPwaManifest({
            name: 'Preact Progressive Web App',
            short_name: 'Preact App',
            description: 'Magento2 Preact Progressive Web App!',
            filename: 'manifest.json',
            publicPath: path.join(__dirname, `../../app/design/${area}/${src}/web/js/`),
            background_color: '#ffffff',
            crossorigin: 'use-credentials',
            icons: [
                {
                    src: path.resolve('./app/src/web/images/logo.png'),
                    sizes: [96, 128, 192, 256, 384, 512]
                }
            ]
        })
    ],
    optimization: {
        namedModules: false,
        namedChunks: false,
        nodeEnv: mode,
        flagIncludedChunks: true,
        occurrenceOrder: true,
        sideEffects: true,
        usedExports: true,
        concatenateModules: true,
        splitChunks: {
            chunks: 'async',
            minSize: 30000,
            minChunks: 1, 
            maxAsyncRequests: 5,
            maxInitialRequests: 3,
            name: true,
            automaticNameDelimiter: '~'
        },
        noEmitOnErrors: true,
        minimize: true,
        minimizer: [
            new ParallelUglifyPlugin({
                cacheDir: '.cache/',
                workerCount: os.cpus().length,
                sourceMap: true,
                uglifyJS: {
                    output: {
                        comments: false,
                        beautify: false
                    },
                    compress: {
                        collapse_vars: true,
                        reduce_vars: true
                    }
                }
            })
        ]
    }
})
const webpackProdConfig: InputConfig = baseConfig.getConfig()

export default webpackProdConfig