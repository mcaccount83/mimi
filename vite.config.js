import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        base: env.VITE_ASSET_BASE_URL || '/build/',
        define: {
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: false,
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
        },
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/js/flash.js',
                    'resources/forum/blade-bootstrap/css/forum.css',
                    'resources/forum/blade-bootstrap/js/forum.js',
                ],
                refresh: true,
            }),
        ],
        resolve: {
            alias: {
                '$': 'jquery',
                'jQuery': 'jquery',
            },
        },
        css: {
            preprocessorOptions: {
                scss: {
                    silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
                }
            }
        }
    };
});
