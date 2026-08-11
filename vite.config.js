import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
    preview: {
        host: 'localhost',
        port: 4173,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-charts.js'],
            refresh: true,
        }),
    ],
});
