<template>
  <nav
    class="glass-header fixed top-0 w-full z-50 border-b border-outline-variant/20 shadow-2xl shadow-blue-900/20"
  >
    <div class="flex justify-between items-center h-20 px-4 sm:px-8 max-w-screen-2xl mx-auto">
      <div class="flex items-center gap-4 lg:gap-8">
        <Link
          href="/"
          class="text-xl sm:text-2xl font-black tracking-tighter text-slate-100 hover:text-primary transition-colors uppercase no-underline"
        >
          TechPulse
        </Link>
        <div v-if="categorias && categorias.length" class="hidden lg:flex gap-2">
          <Link
            v-for="categoria in categorias.slice(0, 3)"
            :key="categoria.id"
            :href="route('home', { categoria: categoria.slug })"
            :class="link_classe(categoria.slug)"
            class="px-3 py-2 rounded-md text-sm font-medium tracking-wide no-underline transition-all duration-300"
          >
            {{ categoria.name }}
          </Link>
        </div>
      </div>
      <div class="flex items-center gap-3 sm:gap-4">
        <Link
          :href="conta_link"
          class="text-on-surface-variant hover:text-primary hover:bg-primary/10 transition-all duration-300 p-2 rounded-full"
          :aria-label="conta_label"
        >
          <span class="material-symbols-outlined">account_circle</span>
        </Link>
        <Link
          href="/#newsletter"
          class="bg-primary text-white px-4 sm:px-6 py-2.5 rounded-lg font-bold text-sm tracking-wide glow-hover transition-all scale-95 active:scale-90 no-underline"
        >
          Inscrever-se
        </Link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({
  categorias: {
    type: Array,
    default: () => [],
  },
  categoria_ativa: {
    type: String,
    default: null,
  },
});

const page = usePage();
const usuario = computed(() => page.props.auth?.user ?? null);
const conta_link = computed(() => (usuario.value ? route("admin.home") : route("login")));
const conta_label = computed(() =>
  usuario.value ? "Ir para o painel administrativo" : "Entrar"
);

const link_classe = (slug) => {
  if (props.categoria_ativa === slug) {
    return "text-primary border-b-2 border-primary";
  }
  return "text-on-surface-variant hover:text-slate-100 hover:bg-primary/10";
};
</script>
