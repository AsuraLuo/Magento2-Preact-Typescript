import * as path from 'path'
import { InputConfig } from '../decorators'
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const resolve = (dir: string) => path.join(__dirname, '../../', dir)

export class WebpackConfig {
    private path: string
    private useConfig: InputConfig

    constructor (config: InputConfig) {
        this.path = config.root || ''
        this.useConfig = config
    }

    public get utils () {
        return {
            assetsPath: (_path?: string) => {
                _path = _path || ''
                let assetsSubDirectory = this.path
                return path.posix.join(assetsSubDirectory, _path)
            },
            cssLoaders: (options?: any) => {
                options = options || {}

                let cssLoader = {
                    loader: 'css-loader',
                    options: {
                        minimize: process.env.NODE_ENV === 'production',
                        sourceMap: options.sourceMap
                    }
                }

                const generateLoaders = (loader?: any, loaderOptions?: any) => {
                    let loaders = [cssLoader]
                    if (loader) {
                        loaders.push({
                            loader: `${loader}-loader`,
                            options: Object.assign({}, loaderOptions, {
                                sourceMap: options.sourceMap
                            })
                        })
                    }
                }

                return {
                    css: generateLoaders(),
                    postcss: generateLoaders(),
                    less: generateLoaders('less'),
                    sass: generateLoaders('sass', { indentedSyntax: true }),
                    scss: generateLoaders('sass'),
                    stylus: generateLoaders('stylus'),
                    styl: generateLoaders('stylus')
                }
            },
            styleLoaders: (options: any) => {
                let output = []
                let loaders = this.utils.cssLoaders(options)
                for (let extension in loaders) {
                    let loader = (<any>loaders)[extension]
                    output.push({
                        test: new RegExp(`\\.${extension}$`),
                        use: loader
                    })
                }
                return output
            }
        }
    }

    setEntry (config: InputConfig) {
        this.useConfig = config
    }

    getConfig () {
        let config = this.useConfig
        let { output } = this.useConfig
        let { entry } = this.useConfig
        let { plugins } = this.useConfig
        delete config.output
        delete config.entry
        delete config.plugins
        delete config.root

        return {
            entry: entry,
            output,
            resolve: {
                extensions: [
                    '.tsx',
                    '.vue',
                    '.ts',
                    '.js', 
                    '.scss', 
                    '.less', 
                    '.stylus', 
                    '.css', 
                    '.json'
                ],
                modules: [
                    resolve('app/src/components/'),
                    resolve('node_modules')
                ],
                alias: {
                    '@components': resolve('app/src/components/')
                }
            },
            module: {
                rules: [
                    {
                        test: /\.js|jsx$/,
                        use: ['happypack/loader?id=happy-babel'],
                        include: [this.path],
                        exclude: [
                            /node_modules/
                        ]
                    },
                    {
                        test: /\.tsx?$/,
                        use: [
                            'cache-loader',
                            'happypack/loader?id=happy-babel',
                            {
                                loader: 'ts-loader',
                                options: { 
                                    happyPackMode: true,
                                    transpileOnly: true,
                                    appendTsxSuffixTo: [/\.jsx$/] 
                                }
                            }
                        ],
                        include: [this.path],
                        exclude: [
                            /node_modules/
                        ]
                    },
                    {
                        test: /\.ts$/,
                        loader: 'ts-loader',
                        include: [this.path],
                        exclude: [
                            /node_modules/
                        ],
                        options: {
                            transpileOnly: true,
                            happyPackMode: true
                        }
                    },
                    {
                        test: /\.(graphql|gql)$/,
                        loader: 'graphql-tag/loader',
                        include: [this.path],
                        exclude: /node_modules/,
                    },
                    {
                        test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
                        loader: 'url-loader',
                        options: {
                            limit: 10000,
                            name: this.utils.assetsPath(`${this.path}/[name].[hash:7].[ext]`)
                        }
                    },
                    {
                        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
                        loader: 'url-loader',
                        options: {
                            limit: 10000,
                            name: this.utils.assetsPath(`${this.path}/[name].[hash:7].[ext]`)
                        }
                    },
                    {
                        test: /\.(woff2?|eot|ttf|otf|rtf)(\?.*)?$/,
                        loader: 'url-loader',
                        options: {
                            limit: 10000,
                            name: this.utils.assetsPath(`${this.path}/[name].[hash:7].[ext]`)
                        }
                    }
                ]
            },
            plugins,
            ...config
        }
    }
}