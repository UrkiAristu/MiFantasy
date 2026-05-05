import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import purgecss from 'vite-plugin-purgecss';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        purgecss({
            content: [
                './resources/**/*.blade.php', // Que busque las clases en tus vistas
                './resources/**/*.js',        // Y en tus scripts
                './resources/**/*.vue'
            ],
            // safelist: Evita que borre clases dinámicas de Bootstrap (ej: modales o menús desplegables)
            safelist: [/^modal-/, /^fade/, /^show/, /^collapse/, /^dropdown-/, /^nav-/]
        })
    ],
});
