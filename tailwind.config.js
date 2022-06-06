module.exports = {
  prefix: 'tw-',
  content: ["./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],

  theme: {
    extend: {},
  },
  plugins: [require('@tailwindcss/forms'),],
}
