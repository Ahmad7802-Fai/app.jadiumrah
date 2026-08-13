export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        jadigreen: {
          DEFAULT: "#0d2f27",   // hijau utama brand
          700: "#0b241e",       // hijau gelap untuk gradient
        },
      },
    },
  },
  plugins: [],
}
