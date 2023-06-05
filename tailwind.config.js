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
                'blue-50': '#1168cd',
                'orange-50': '#fa4616',
                'gray-neutral-55': '#595959',
                'gray-neutral-25': '#e3e3e3',
                'gray-cold-15': '#f6f6f6',
                'gray-neutral-05': '#fafafa',
                'pink-50': '#dc2560',
                'yellow-50': '#f4a700',
                'purple-50': '#8b3d8a',
                'cyan-50': '#0ba2b3',
                'green-50': '#95d041',
                'error-50': '#f5222d',
                'success-50': '#52c41a',
                'warning-50': '#faad14',
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
