<template>
  <header
    class="glass-header fixed top-0 right-0 z-50 flex h-16 w-full items-center justify-between border-b border-outline-variant/20 px-4 shadow-2xl md:w-[calc(100%-16rem)] md:px-8"
  >
    <div class="flex min-w-0 items-center gap-3 md:gap-4">
      <button
        type="button"
        class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-secondary-container hover:text-on-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 md:hidden"
        aria-label="Abrir menu"
        @click="$emit('toggle-sidebar')"
      >
        <span class="material-symbols-outlined">menu</span>
      </button>
      <h2 class="font-headline text-lg font-extrabold whitespace-nowrap text-on-surface md:text-xl">
        TechPulse Admin
      </h2>
      <span class="hidden text-on-surface-variant md:inline">/</span>
      <span class="hidden border-b-2 border-primary pb-1 font-bold whitespace-nowrap text-primary md:inline">
        {{ breadcrumb }}
      </span>
    </div>

    <div class="flex items-center gap-2 md:gap-6">
      <div class="relative hidden md:block">
        <span
          class="material-symbols-outlined absolute top-1/2 left-3 -translate-y-1/2 text-sm text-on-surface-variant"
          aria-hidden="true"
        >search</span>
        <input
          v-model="termo_busca"
          type="search"
          aria-label="Buscar"
          :placeholder="placeholder_busca"
          class="w-64 rounded-full border border-outline-variant/20 bg-surface-container-high py-2 pr-4 pl-10 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/60 focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/40"
        />
      </div>
      <div class="flex items-center gap-3 border-l border-outline-variant/20 pl-3 md:gap-4 md:pl-6">
        <button
          type="button"
          :aria-label="label_tema"
          :title="label_tema"
          class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
          @click="alternar_tema"
        >
          <span class="material-symbols-outlined">{{ icone_tema }}</span>
        </button>
        <button
          type="button"
          aria-label="Notificações"
          class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        >
          <span class="material-symbols-outlined">notifications</span>
        </button>
        <button
          type="button"
          aria-label="Ajuda"
          class="hidden rounded-lg p-1.5 text-on-surface-variant transition-colors hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 md:block"
        >
          <span class="material-symbols-outlined">help_outline</span>
        </button>
        <span
          class="ml-1 flex h-8 w-8 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container-high text-on-surface-variant"
          aria-hidden="true"
        >
          <span class="material-symbols-outlined text-sm">account_circle</span>
        </span>
      </div>
    </div>
  </header>
</template>

<script>
import { computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { use_theme } from "@/Composables/use_theme";
import { use_admin_busca } from "@/Composables/use_admin_busca";

export default {
    name: "Topbar",
    props: {
        breadcrumb: { type: String, default: "Admin" },
    },
    emits: ["toggle-sidebar"],
    setup() {
        const page = usePage();
        const { tema_atual, alternar_tema, inicializar_tema } = use_theme();
        const { termo_busca } = use_admin_busca();

        onMounted(() => {
            inicializar_tema();
        });

        const placeholder_busca = computed(() => {
            if (page.component === "Admin/Posts/PostsIndex") {
                return "Buscar posts...";
            }
            if (page.component === "Admin/Categories/CategoriesIndex") {
                return "Buscar categorias...";
            }
            return "Buscar...";
        });

        const icone_tema = computed(() => {
            return tema_atual.value === "dark" ? "light_mode" : "dark_mode";
        });

        const label_tema = computed(() => {
            return tema_atual.value === "dark" ? "Ativar modo claro" : "Ativar modo escuro";
        });

        return {
            tema_atual,
            alternar_tema,
            icone_tema,
            label_tema,
            termo_busca,
            placeholder_busca,
        };
    },
};
</script>
