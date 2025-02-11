import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
// import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd());

    process.env = {
        ...process.env,
        ...env
    };

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        // resolve: {
        //     alias: {
        //         '@': path.resolve('resources/js'),
        //         'ziggy-js': path.resolve('vendor/tightenco/ziggy/dist/vue.es.js'),
        //     },
        // },
        server: {
            proxy: {
                '/': env.APP_URL,
            },
        },
    };
});