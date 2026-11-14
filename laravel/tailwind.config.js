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
                sstn: {
                    navy: '#00295c',
                    blue: '#0080d0',
                    red: '#df0220',
                    gray: '#f2f4f7',
                    ink: '#3a4250',
                },
            },
        },
    },

    plugins: [forms],
};
