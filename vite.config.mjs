import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss", // add SCSS file
                "resources/forum/blade-bootstrap/css/forum.css", // add CSS file
                "resources/js/app.js", // add JavaScript file
                "resources/forum/blade-bootstrap/js/forum.js", // add JS file for forum
            ],
            refresh: true,
        }),
    ],
});


