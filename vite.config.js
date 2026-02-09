import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/js/chart-config.js',
        ]),
    ],server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'main.luii.my.id', // e.g., '192.168.1.100' or 'your-domain.com'
            protocol: 'ws',
        },
    },
});
