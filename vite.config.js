import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import eslint from 'vite-plugin-eslint'

export default defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ['resources/js/app.js', 'resources/css/style.css'],
      refresh: true,
    }),
    eslint(),
  ],
  build: {
    outDir: path.resolve(__dirname, 'public/build'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'resources/js/app.js'),
      output: {
        chunkFileNames: '[name].js',
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
  server: {
    watch: {
      usePolling: true,
      ignored: ['!**/resources/js/**'],
    },
  },
});
