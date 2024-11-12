import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
				'resources/js/vue/auth/register.js',
				'resources/js/vue/auth/login.js',
				'resources/js/vue/auth/identify.js',
				'resources/js/vue/jstester/view.js',
            ],
            refresh: true,
        }),
		react(),
		vue(),
    ],
});
