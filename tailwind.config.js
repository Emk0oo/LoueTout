/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    colors: {
      accent: 'var(--accent-color)',
      blue: "#007bff",
    },
    extend: {
      
    },
  },
  plugins: [],
}
