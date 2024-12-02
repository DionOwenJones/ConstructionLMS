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
        minify: 'terser',
        cssMinify: true,
    },
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    },
    css: {
        postcss: './postcss.config.cjs'
    }
});