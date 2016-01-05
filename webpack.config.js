var webpack = require('webpack'),
    path = require('path'),
    branch = require('git-branch'),
    ExtractTextPlugin = require('extract-text-webpack-plugin'),
    srcPath = path.join(__dirname, 'src/Themosis/_assets'),
    autoprefixer = require('autoprefixer'),
    precss = require('precss');

module.exports = {
    target: 'web',
    cache: true,
    entry: {
        '_themosisCore': path.join(srcPath, 'components/components.js')
    },
    output: {
        path: path.join(srcPath, 'js'),
        publicPath: '',
        filename: '[name].js'
    },
    resolve: {
      extensions: ['', '.js', '.css', '.styl']
    },
    module: {
      loaders: [
          // Extract CSS files
          {
              test: /\.css$/,
              loader: ExtractTextPlugin.extract('css', 'css-loader')
          },
          // Extract STYLUS files
          {
              test: /\.styl$/,
              loader: ExtractTextPlugin.extract('stylus', 'css-loader?minimize!postcss-loader!stylus-loader')
          }
      ]
    },
    postcss: function()
    {
        // Function called by the `postcss-loader`.
        // Apply autoprefixr to css output.
        return [autoprefixer, precss];
    },
    plugins: [
        // Extract CSS chunks to an external file.
        new ExtractTextPlugin('../css/[name].css', {allChunks: true}),
        new webpack.NoErrorsPlugin()
    ]
};