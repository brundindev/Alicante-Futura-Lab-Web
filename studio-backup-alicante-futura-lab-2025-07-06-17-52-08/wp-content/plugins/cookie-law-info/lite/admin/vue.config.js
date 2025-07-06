const path = require("path");
module.exports = {
  productionSourceMap: process.env.NODE_ENV === 'production'
  ? false
  : true,
  css: {
    loaderOptions: {
      sass: {
        additionalData: `
          @import "@/scss/_mixins.scss";
          @import "@/scss/_functions.scss";
          @import "@/scss/_variables.scss";
        `
      }
    }
  },
  // tweak internal webpack configuration.
  // see https://github.com/vuejs/vue-cli/blob/dev/docs/webpack.md
  filenameHashing: false,
  publicPath: '/wp-content/plugins/cookie-law-info/admin/dist',
  transpileDependencies: [
    '@wordpress/hooks',
    '@wordpress/i18n'
  ],
  configureWebpack: {
    module: {
      rules: [
        {
          test: /\.m?js$/,
          include: /node_modules\/@wordpress/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                ['@babel/preset-env', {
                  targets: {
                    node: 'current'
                  }
                }]
              ],
              plugins: [
                '@babel/plugin-proposal-optional-chaining',
                '@babel/plugin-proposal-nullish-coalescing-operator'
              ]
            }
          }
        }
      ]
    }
  }
}