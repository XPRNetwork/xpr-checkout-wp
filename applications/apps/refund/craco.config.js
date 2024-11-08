const path = require("path");

module.exports = {
  webpack: {
    configure: webpackConfig => {
      webpackConfig.module.rules.push({
        test: /\.(ts|tsx)$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: [
              "@babel/preset-env",
              "@babel/preset-react",
              "@babel/preset-typescript",
            ],
          },
        },
      });

      webpackConfig.module.rules.push({
        test: /\.m?js$/,
        resolve: {
          fullySpecified: false,
        },
      });

      // Set the output path to your desired location
      if (process.env.NODE_ENV === "production") {
        webpackConfig.output.path = path.resolve(
          __dirname,
          "../../../dist/refund"
        );

        // Set the filenames for JS and CSS to your specific file names
        webpackConfig.output.filename = "static/js/app.js";
        webpackConfig.output.chunkFilename = "static/js/[name].chunk.js";

        webpackConfig.plugins.forEach(plugin => {
          if (plugin.constructor.name === "MiniCssExtractPlugin") {
            plugin.options.filename = "static/css/app.css";
            plugin.options.chunkFilename = "static/css/[name].chunk.css";
          }
        });
      }

      return webpackConfig;
    },
  },
};
