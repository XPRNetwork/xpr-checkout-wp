module.exports = {
  content: ["./js/**/*.{svelte,js,ts}"],
  daisyui: {
    themes: [
      "light",
      "dark",
      "acid",
      "synthwave",
      "cyberpunk",
      {
        acidex: {
          ...require("daisyui/src/theming/themes")["acid"],
          primary: "#7c3bed",
        },
      },
    ],
  },
  plugins: [require("daisyui")],
};
