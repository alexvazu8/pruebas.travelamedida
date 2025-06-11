import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.{html,js,php,scss}',  // Asegúrate de incluir los archivos .scss
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                orange: {
                    500: '#f97316', // Naranja estándar
                    600: '#ea580c', // Naranja más oscuro
                  },
                primary: {
                    DEFAULT: '#1E3A8A', // Azul oscuro
                    light: '#3B82F6',   // Azul claro
                },
                accent: '#F97316',    // Naranja
                neutral: '#FFFFFF',   // Blanco
            },
        },
    },
    plugins: [],
};
