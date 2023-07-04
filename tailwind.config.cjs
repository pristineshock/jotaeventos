const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}"],
  theme: {
    extend: {
      fontFamily: {
        abel: ["'Abel'", ...defaultTheme.fontFamily.sans],
        inter: ["'Inter Variable'", ...defaultTheme.fontFamily.sans],
        hamburger: ["'Hamburger'", ...defaultTheme.fontFamily.sans],
        walkway: ["'Walkway'", ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [],
};
