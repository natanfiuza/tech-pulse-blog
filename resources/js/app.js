
import './bootstrap';
import "./signature";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"; //Importe isso!
import { ZiggyVue } from "ziggy-js";
import { Ziggy } from './ziggy'; // Importe o arquivo ziggy.js gerado
import "../css/app.css"; // Se você tiver CSS global
import "./helpers";
import '../sass/custom.scss'; //  Import o custom.scss

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue,Ziggy)
            .mount(el);
    },
});
