import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    server: {
        host: '0.0.0.0',       // bind inside container so host can reach it
        port: 5173,            // internal port (container side)
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 5174,        // the HOST port your browser connects to
        },
        watch: {
            usePolling: true,  // needed for file changes to be detected on Windows/Docker
        },
    },
});
