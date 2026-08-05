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
                pos: {
                    50: 'rgb(227, 242, 253)',
                    200: 'rgb(144, 202, 249)',
                    500: 'rgb(33, 150, 243)',
                    900: 'rgb(13, 71, 161)',
                },
            },
        },
    },

    plugins: [forms],
};