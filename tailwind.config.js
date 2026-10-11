import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fdf3f4',
                    100: '#fbe4e6',
                    200: '#f6c9cd',
                    300: '#efa1a8',
                    400: '#e56a76',
                    500: '#d63a4a',
                    600: '#c81e2b',
                    700: '#a81823',
                    800: '#8b1620',
                    900: '#74161e',
                },
            },
        },
    },

    plugins: [forms],
};
