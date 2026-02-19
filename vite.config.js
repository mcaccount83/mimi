import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const isProduction = env.APP_ENV === 'Production';

    return {
        base: isProduction ? '/mimi/build/' : '/build/',
        plugins: [
            laravel({
                input: [
                        'resources/sass/app.scss',
                        'resources/js/app.js',
                        'resources/js/flash.js',
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
        build: {
            rollupOptions: {
                external: ['jquery'],
                output: {
                    globals: {
                        jquery: '$'
                    }
                }
            }
        }
    };
});
