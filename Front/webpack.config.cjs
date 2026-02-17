const path = require('path');

module.exports = {
  mode: 'development', // یا 'production' یا 'none'
  entry: './src/webcomponents.js',
  output: {
    filename: 'react-components.js',
    path: path.resolve(__dirname, 'dist'),
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-react'],
          },
        },
      },
    ],
  },
};