<template>
  <aside
    class="fixed top-0 left-0 z-40 flex h-screen w-64 flex-col border-r border-outline-variant/20 bg-background py-6 px-4 transition-transform duration-300 md:translate-x-0"
    :class="open ? 'translate-x-0' : '-translate-x-full'"
    :aria-hidden="is_mobile && !open ? 'true' : null"
    :inert="is_mobile && !open"
  >
    <!-- Header -->
    <div class="mb-10 px-4">
      <h1 class="font-display text-2xl font-black tracking-tight text-on-primary-fixed">TechPulse</h1>
      <p class="mt-1 text-sm text-on-surface-variant">Admin Console</p>
    </div>

    <!-- CTA -->
    <div class="mb-8 px-2">
      <Link
        href="/admin/posts/create"
        class="glow-hover flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 px-4 font-medium text-on-primary transition-all duration-300 hover:bg-surface-tint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
      >
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
        Novo Post
      </Link>
    </div>

    <!-- Navegação principal -->
    <nav class="flex-1 space-y-2 px-2" aria-label="Navegação principal">
      <Link
        v-for="item in menu_items"
        :key="item.href"
        :href="item.href"
        class="flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-300 active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        :class="
          item.active
            ? 'bg-secondary-container text-on-surface'
            : 'text-on-surface-variant hover:bg-secondary-container hover:text-on-surface'
        "
      >
        <span class="material-symbols-outlined">{{ item.icon }}</span>
        <span class="font-body text-sm font-medium">{{ item.label }}</span>
      </Link>
    </nav>

    <!-- Navegação do rodapé -->
    <div class="mt-auto space-y-2 border-t border-outline-variant/20 px-2 pt-4">
      <div
        class="flex cursor-default items-center gap-3 rounded-lg px-4 py-3 text-on-surface-variant/60"
        title="Perfil (em breve)"
      >
        <span class="material-symbols-outlined">account_circle</span>
        <span class="font-body text-sm font-medium">Perfil</span>
      </div>
      <Link
        href="/logout"
        class="flex items-center gap-3 rounded-lg px-4 py-3 text-error/80 transition-all duration-300 hover:bg-error/10 hover:text-error focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error/60"
      >
        <span class="material-symbols-outlined">logout</span>
        <span class="font-body text-sm font-medium">Sair</span>
      </Link>
    </div>
  </aside>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    components: { Link },
    props: {
        open: { type: Boolean, default: false },
    },
    emits: ["close"],
    data() {
        return {
            is_mobile: false,
            mql: null,
        };
    },
    computed: {
        menu_items() {
            const componente = this.$page.component;
            return [
                {
                    label: "Dashboard",
                    icon: "dashboard",
                    href: "/admin/home",
                    active: componente === "Admin/AdminHome",
                },
                {
                    label: "Posts",
                    icon: "article",
                    href: "/admin/posts",
                    active: componente.startsWith("Admin/Posts"),
                },
                {
                    label: "Categorias",
                    icon: "category",
                    href: "/admin/categories",
                    active: componente.startsWith("Admin/Categories"),
                },
            ];
        },
    },
    mounted() {
        this.mql = window.matchMedia("(max-width: 767px)");
        this.is_mobile = this.mql.matches;
        this.mql.addEventListener("change", this.check_mobile);
    },
    beforeUnmount() {
        if (this.mql) {
            this.mql.removeEventListener("change", this.check_mobile);
        }
    },
    methods: {
        check_mobile(event) {
            this.is_mobile = event.matches;
        },
    },
};
</script>
