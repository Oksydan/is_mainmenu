const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');
const { EsbuildPlugin } = require('esbuild-loader');

module.exports = {
  entry: {
    form: [
      './src/js/form.js',
      './src/css/form.scss',
    ],
    grid: [
      './src/js/grid.js',
      './src/css/grid.scss',
    ],
  },
  output: {
    filename: 'js/admin/[name].js',
    path: path.resolve(__dirname, '../views/'),
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'esbuild-loader',
          options: {
            target: 'es2015'
          }
        }
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                config: path.resolve(__dirname, 'postcss.config.js'),
              },
            }
          },
          {
            loader: 'sass-loader',
            options: {
              implementation: require('sass')
            },
          },
        ]
      }
    ]
  },
  stats: {
    colors: true,
  },
  devtool: 'source-map',
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/admin/[name].css',
    }),
    new EsbuildPlugin({
      target: 'es2016',
      format: 'iife',
      minify: true,
    }),
  ],
};
