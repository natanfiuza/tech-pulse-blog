import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from 'path'; // Importe o módulo 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
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
    resolve: {
        // Adicione esta seção para o alias
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
            "~bootstrap": path.resolve(__dirname, "node_modules/bootstrap"), // Alias para o Bootstrap
        },
    },
    // define: {
    //     // Adicione esta seção!
    //     "process.env": {}, // Define process.env como um objeto vazio
    //     "process.cwd": () => "", // Adicione esta linha!
    // },
    build: {
        rollupOptions: {
            external: ["prismjs"],
        },
    },
  
});
