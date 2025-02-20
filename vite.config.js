import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
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
