import babel from "rollup-plugin-babel";

export default {
  input: "js/blocks/block.jsx",
  output: {
    file: "../dist/block.js",
    format: "iife", // Format IIFE pour exécution dans le navigateur
    name: "MyBundle", // Nom global pour la fonction
  },
  plugins: [
    babel({
      exclude: "node_modules/**",
      presets: [
        [
          "@babel/preset-env",
          {
            targets: {
              ie: "11",
            },
            modules: false, // Assurez-vous que Babel ne convertit pas en CommonJS
          },
        ],
        "@babel/preset-react",
      ],
    }),
  ],
};
