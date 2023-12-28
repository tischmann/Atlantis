/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './app/Views/**/*.{tpl,php}',
        './resources/js/*.js',
        './public/*.js',
        './node_modules/tw-elements/dist/js/**/*.js'
    ],
    theme: {
        extend: {}
    },
    plugins: [require('tw-elements/dist/plugin.cjs')],
    darkMode: 'class'
}
