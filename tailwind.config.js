import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: 'rgb(var(--color-primary, 225 106 55) / <alpha-value>)',
                secondary: 'rgb(var(--color-secondary, 1 128 61) / <alpha-value>)',
                'secondary-green': 'rgb(var(--color-secondary-green, 1 128 61) / <alpha-value>)',
                'background-light': 'rgb(var(--color-background-light, 248 246 246) / <alpha-value>)',
                'background-dark': 'rgb(var(--color-background-dark, 33 22 17) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['var(--font-display)', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
