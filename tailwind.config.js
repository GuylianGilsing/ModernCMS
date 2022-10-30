/** @type {import('tailwindcss').Config} */
module.exports = {
    content: {
        relative: true,
        files: [
            "./cms/core/views/templates/**/*.{html,twig}",
            "./a-modules-src/core-auth/src/views/templates/**/*.{html,twig}"
        ],
    },
    theme: {
        extend: {
            colors: {
                'primary': '#00A8FF',
                'primary-hover': '#3FBFFF',
            }
        },
    },
    plugins: [],
}
