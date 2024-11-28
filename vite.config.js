import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            external: [
                'jquery',
                'alpinejs',
                '@alpinejs/focus'
            ]
        },
        // Add these options to handle the build environment better
        minify: true,
        sourcemap: false
    },
    server: {
        hmr: {
            host: 'localhost'
        }
    }
});
