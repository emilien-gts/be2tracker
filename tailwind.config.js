const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: '',
    content: [
        "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {
            colors: {
                orange: colors.orange,
                green: colors.green,
                red: colors.red,
                blue: colors.blue,
                gray: colors.gray,
                purple: colors.purple,
            },
        },
    },
    plugins: [],
    safelist: [
        {
            pattern: /bg-(red|green|blue|yellow|purple|pink|gray|indigo|teal|orange|cyan)-(100|200|300|400|500|600|700|800|900)|text-(red|green|blue|yellow|purple|pink|gray|indigo|teal|orange|cyan)-(100|200|300|400|500|600|700|800|900)/,
        }
    ],

}