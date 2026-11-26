import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// In Docker, Vite runs in the `node` service (compose.yml) and the browser
// reaches it on localhost:5174. Outside Docker it runs on the same port.
// (Port 8000 is left free for `php artisan serve`.)
const inDocker = process.env.VITE_IN_DOCKER === '1';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/3dMain.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: inDocker ? '0.0.0.0' : '127.0.0.1',
        port: 5174,
        strictPort: true,
        hmr: inDocker ? { host: 'localhost' } : undefined,
        watch: {
            // Changes made on Windows don't always emit events inside the container.
            usePolling: inDocker,
            interval: 300,
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
});
