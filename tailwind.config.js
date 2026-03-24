import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#103529',
                    50:  '#e6efed',
                    100: '#cddfdb',
                    200: '#9bbfb7',
                    300: '#6aa093',
                    400: '#3f7f6d',
                    500: '#103529',
                    600: '#0e2f24',
                    700: '#0b251c',
                    800: '#071a14',
                    900: '#03100c',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
}