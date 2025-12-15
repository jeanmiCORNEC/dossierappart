import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                // Primary (Brand/Action)
                primary: {
                    DEFAULT: '#4F46E5',
                    50: '#ECEAFD',
                    100: '#DCD8FC',
                    200: '#BAB4F9',
                    300: '#9891F6',
                    400: '#756DF3',
                    500: '#5349E9',
                    600: '#4F46E5',
                    700: '#3730A3',
                    800: '#2E2886',
                    900: '#1E1B4B',
                },
                // Secondary (Trust)
                secondary: {
                    DEFAULT: '#0F172A',
                    light: '#1E293B',
                },
                // Success (Security)
                success: {
                    DEFAULT: '#10B981',
                    light: '#D1FAE5',
                },
                // Warning (Alert/Speed)
                warning: {
                    DEFAULT: '#F59E0B',
                    light: '#FEF3C7',
                },
                // Danger
                danger: {
                    DEFAULT: '#EF4444',
                    light: '#FEE2E2',
                },
                // Surfaces
                surface: {
                    light: '#F8FAFC',
                    dark: '#1E1B4B',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'glow': '0 0 20px rgba(79, 70, 229, 0.3)',
                'glow-lg': '0 0 40px rgba(79, 70, 229, 0.4)',
            },
        },
    },

    plugins: [forms],
};
