
import './bootstrap';
import "./signature";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"; //Importe isso!
import { ZiggyVue } from "ziggy-js";
import { Ziggy } from './ziggy'; // Importe o arquivo ziggy.js gerado
import "../css/tailwind.css"; // Tailwind v3 (Midnight Pulse)
import "./helpers";
import AnalyticsScript from "@/Components/AnalyticsScript.vue";
import ConsentBanner from "@/Components/ConsentBanner.vue";

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        createApp({
            render() {
                const eh_admin = props.initialPage.component.startsWith('Admin/');

                return h('div', { class: 'techpulse-app-root' }, [
                    h(App, props),
                    !eh_admin ? h(AnalyticsScript) : null,
                    !eh_admin ? h(ConsentBanner) : null,
                ]);
            },
        })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
});
