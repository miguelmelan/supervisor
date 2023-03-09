const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
        'node_modules/flowbite/**/*.{js,jsx,ts,tsx}',
    ],

    theme: {
        extend: {
            colors: {
                'blue-50': '#0067DF',
                'orange-50': '#FA4616',
                'gray-neutral-55': '#595A5C',
                'gray-neutral-25': '#E1E2E4',
                'gray-cold-15': '#F1F6F8',
                'gray-neutral-05': '#FAFAFA',
                'pink-50': '#ED145B',
                'yellow-50': '#FFB40E',
                'purple-50': '#933692',
                'cyan-50': '#38C6F4',
                'green-50': '#34DE69',
                'error-50': '#F5222D',
                'success-50': '#52C41A',
                'warning-50': '#FAAD14',
            },
            fontFamily: {
                sans: ['Noto Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('flowbite/plugin'),
    ],
};
