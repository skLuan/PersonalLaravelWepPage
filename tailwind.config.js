import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                'skl-nunito': ['"Nunito Sans"', 'serif'],
                'skl-titles': ['"Chakra Petch"', 'serif']
            },
            colors: {
                'skl-black': '#0A090B',
                'skl-grey': '#101828',
                'skl-white': {
                    DEFAULT: '#D9D9D9',
                    'pink': '#EBE9FE',
                    'true': '#FFFFFF',
                },
                'skl-pink': {
                    DEFAULT: '#DC105F',
                },
                'skl-yellow': {
                    DEFAULT: '#FFC030',
                },
                'skl-purple': '#301770',
            }
        },
    },

    plugins: [forms],
};
