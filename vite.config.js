import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        base: env.VITE_ASSET_BASE_URL || '/build/',
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        resolve: {
            alias: {
                '$': 'jquery',
                'jQuery': 'jquery',
            },
        },
        build: {
            rollupOptions: {
                external: ['jquery'],
                output: {
                    globals: {
                        jquery: '$'
                    }
                }
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
