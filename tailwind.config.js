/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    colors: {
      accent: 'var(--accent-color)',
      secondary: 'var(--secondary-color)',
    },
    extend: {
      
    },
  },
  plugins: [],
}
