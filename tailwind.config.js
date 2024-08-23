import defaultTheme from 'tailwindcss/defaultTheme';

const svgToDataUri = require("mini-svg-data-uri");
const colors = require("tailwindcss/colors");
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
            colors: () => {
                return {
                    emerald: colors.amber,
                    gray: {
                        50: "#fafafa",
                        100: "#f4f4f5",
                        150: "#ececed",  // New mid-range color
                        200: "#e4e4e7",
                        250: "#dcdcdf",  // New mid-range color
                        300: "#d4d4d8",
                        350: "#bbbbc1",  // New mid-range color
                        400: "#a1a1aa",
                        450: "#8c8c94",  // New mid-range color
                        500: "#71717a",
                        550: "#63636b",  // New mid-range color
                        600: "#52525b",
                        650: "#494951",  // New mid-range color
                        700: "#3f3f46",
                        750: "#353538",  // New mid-range color
                        800: "#27272a",
                        850: "#202022",  // New mid-range color
                        900: "#18181b",
                        950: "#09090b"
                    },
                    primary: "#BFBFBF",
                    secondary: "#F000B9",
                    info: colors.amber["500"],
                    success: colors.green["500"],
                    warning: "#ff9800",
                    error: "#ff5724",
                    accent: "#5f5af6",
                };
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', ...defaultTheme.fontFamily.sans]
            },
            backgroundImage: (theme) => ({
                'multiselect-caret': `url("${svgToDataUri(
                    `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg>`,
                )}")`,
                'multiselect-spinner': `url("${svgToDataUri(
                    `<svg viewBox="0 0 512 512" fill="${theme('colors.emerald.500')}" xmlns="http://www.w3.org/2000/svg"><path d="M456.433 371.72l-27.79-16.045c-7.192-4.152-10.052-13.136-6.487-20.636 25.82-54.328 23.566-118.602-6.768-171.03-30.265-52.529-84.802-86.621-144.76-91.424C262.35 71.922 256 64.953 256 56.649V24.56c0-9.31 7.916-16.609 17.204-15.96 81.795 5.717 156.412 51.902 197.611 123.408 41.301 71.385 43.99 159.096 8.042 232.792-4.082 8.369-14.361 11.575-22.424 6.92z"></path></svg>`,
                )}")`,
                'multiselect-remove': `url("${svgToDataUri(
                    `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>`,
                )}")`,
            }),
            transitionProperty: {
                'spacing': 'margin, padding',
            }
        }
    },

    plugins: [
        require("@tailwindcss/typography"),
        require("tailwindcss-gradient"),
        require("tailwind-scrollbar-hide"),
        require("tailwind-scrollbar"),
        require("tailwind-bootstrap-grid")({
            containerMaxWidths: {
                sm: "540px",
                md: "720px",
                lg: "960px",
                xl: "1140px",
            },
        }),
    ],
    corePlugins: {
        container: false,
        textOpacity: false,
        backgroundOpacity: true,
        borderOpacity: false,
        divideOpacity: false,
        placeholderOpacity: false,
        ringOpacity: true,
    },
    darkMode: "class",
    mode: "jit",
};
