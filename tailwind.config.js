import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas: 'rgb(var(--canvas) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                'surface-2': 'rgb(var(--surface-2) / <alpha-value>)',
                ink: 'rgb(var(--ink) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
                accent: 'rgb(var(--accent) / <alpha-value>)',
                'accent-fg': 'rgb(var(--accent-fg) / <alpha-value>)',
                line: 'rgb(var(--line) / <alpha-value>)',
                good: 'rgb(var(--good) / <alpha-value>)',
                warn: 'rgb(var(--warn) / <alpha-value>)',
                danger: 'rgb(var(--danger) / <alpha-value>)',
            },
            boxShadow: {
                card: '0 1px 2px rgb(0 0 0 / 0.04), 0 10px 30px -18px rgb(0 0 0 / 0.35)',
                glow: '0 0 0 1px rgb(var(--line)), 0 18px 40px -20px rgb(var(--glow) / 0.45)',
            },
        },
    },

    plugins: [forms],
};
