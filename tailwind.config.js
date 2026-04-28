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
                pink: {
                    50: '#FDE8EE',
                    100: '#FBD3DF',
                    200: '#F7A7BF',
                    300: '#F37B9F',
                    400: '#EF4F7F',
                    500: '#E8375A',
                    600: '#C92E4B',
                    700: '#A5253C',
                    800: '#811C2D',
                    900: '#3D0E17',
                },
            },
        },
    },

    plugins: [forms],
};
