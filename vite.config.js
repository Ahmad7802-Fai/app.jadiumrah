import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: process.env.VITE_HOST ?? '127.0.0.1',
        port: Number(process.env.VITE_PORT ?? 5173),
        strictPort: true,
    },

    // 🔥 TAMBAHKAN INI
    css: {
        preprocessorOptions: {
            scss: {
                includePaths: ['node_modules'],
            },
        },
    },

    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/scss/website/website.scss',
                'resources/scss/jamaah/jamaah.scss',
                'resources/scss/cabang.scss',
                'resources/scss/agent.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});