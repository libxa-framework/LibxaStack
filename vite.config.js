import { defineConfig } from 'vite'
import react     from '@vitejs/plugin-react'
import vue       from '@vitejs/plugin-vue'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'path'

/**
 * LibxaFrame — Vite Configuration
 *
 * Supports React, Vue 3, Svelte and plain JS/TS.
 * Toggle plugins based on what your app uses.
 */
export default defineConfig({
    plugins: [
        tailwindcss(),
        react(),    // Remove if not using React
        vue(),      // Remove if not using Vue
        svelte(),   // Remove if not using Svelte
    ],

    // Dev server — proxies asset requests to PHP server
    server: {
        host: '0.0.0.0',
        port: 5173,
        proxy: {
            // Everything except /resources goes to PHP
            '^(?!/(resources|@vite))': {
                target: 'http://127.0.0.1:8000',
                changeOrigin: true,
            },
        },
        hmr: {
            host: 'localhost',
        },
    },

    // Build settings
    build: {
        outDir: 'src/public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: resolve(__dirname, 'src/resources/js/app.js'),
                style: resolve(__dirname, 'src/resources/css/app.css'),
            },
            output: {
                // Hash filenames for cache busting
                entryFileNames: '[name]-[hash].js',
                chunkFileNames: '[name]-[hash].js',
                assetFileNames: '[name]-[hash].[ext]',
            },
        },
        manifest: true, // Generates manifest.json for PHP to read
        sourcemap: false,
    },

    // Aliases
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src/resources/js'),
            '@css': resolve(__dirname, 'src/resources/css'),
            '@components': resolve(__dirname, 'src/resources/js/components'),
            '@pages': resolve(__dirname, 'src/resources/js/pages'),
            '@stores': resolve(__dirname, 'src/resources/js/stores'),
        },
    },

    // CSS
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "@css/variables";`,
            },
        },
    },
})
