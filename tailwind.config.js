// tailwind.config.js
module.exports = {
  content: [
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
        mono: ['Fira Code', 'ui-monospace'],
      },
    },
  },
  plugins: [
    // require('tw-animate-css'),
  ],
}
