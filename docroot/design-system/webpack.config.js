const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const stylesHandler = MiniCssExtractPlugin.loader;

const isProduction = process.env.NODE_ENV === "production";

const config = {
  entry: "./components/index.js",
  output: {
    filename: 'ds-drupal.js',

    // output to the theme for .libraries.yml consumption
    path: path.resolve(__dirname, "../themes/custom/vagovclaro/dist"),

    clean: false, // so we don't overwrite styles generated by `composer va:theme:compile`
  },
  plugins: [
    // Add your plugins here
    // Learn more about plugins from https://webpack.js.org/configuration/plugins/
  ],
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/i,
        loader: "babel-loader",
      },
      {
        test: /\.(s[ac]ss|css)$/i,
        use: [stylesHandler, "css-loader", "sass-loader"],
      },
      {
        test: /\.twig/,
        use: [
          // Output twig files to theme as well for easy namespacing. See vagovclaro.info.yml.
          {
            loader: 'file-loader',
            options: {
              emitFile: isProduction,
              name: '[path][name].[ext]'
            },
          },
        ],
      },
      {
        test: /\.(eot|svg|ttf|woff|woff2|png|jpg|gif)$/i,
        type: "asset",
      },

      // Add your rules for custom modules here
      // Learn more about loaders from https://webpack.js.org/loaders/
    ],
  },
};

module.exports = () => {
  if (isProduction) {
    config.mode = "production";

    // Styles get extracted to this file.
    config.plugins.push(new MiniCssExtractPlugin({
      filename: 'ds-drupal.styles.css'
    }));
  } else {
    config.mode = "development";
  }
  return config;
};