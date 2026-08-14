import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Binds to localhost by default. Set VITE_HOST (e.g. "0.0.0.0") only when
// you deliberately need the dev/preview server reachable from the LAN or a
// tunnel (ngrok) — that also exposes unbundled source and the HMR socket to
// anyone who can reach the port, so it's an opt-in, not a default.
const devHost = process.env.VITE_HOST || 'localhost';

export default defineConfig({
    server: {
        host: devHost,
        port: 5173,
        origin: process.env.VITE_DEV_SERVER_ORIGIN || undefined,
        hmr: {
            ...(process.env.VITE_HMR_HOST ? { host: process.env.VITE_HMR_HOST } : {}),
            ...(process.env.VITE_HMR_PROTOCOL ? { protocol: process.env.VITE_HMR_PROTOCOL } : {}),
            ...(process.env.VITE_HMR_CLIENT_PORT ? { clientPort: Number(process.env.VITE_HMR_CLIENT_PORT) } : {}),
        },
    },
    preview: {
        host: devHost,
        port: 4173,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-charts.js'],
            refresh: true,
        }),
    ],
});
