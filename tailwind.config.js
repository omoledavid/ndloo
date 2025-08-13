/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "node_modules/preline/dist/*.js",
        "./resources/views/filament/tables/columns/*.blade.php"
    ],
    theme: {
        extend: {},
    },
    plugins: [require("preline/plugin")],
};
