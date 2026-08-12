import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        origin: process.env.VITE_DEV_SERVER_ORIGIN || undefined,
        hmr: {
            ...(process.env.VITE_HMR_HOST ? { host: process.env.VITE_HMR_HOST } : {}),
            ...(process.env.VITE_HMR_PROTOCOL ? { protocol: process.env.VITE_HMR_PROTOCOL } : {}),
            ...(process.env.VITE_HMR_CLIENT_PORT ? { clientPort: Number(process.env.VITE_HMR_CLIENT_PORT) } : {}),
        },
    },
    preview: {
        host: '0.0.0.0',
        port: 4173,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-charts.js'],
            refresh: true,
        }),
    ],
});
