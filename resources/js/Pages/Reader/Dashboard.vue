<template>
  <div class="min-h-screen bg-background text-on-background font-body flex flex-col">
    <Navbar />

    <main class="flex-grow w-full max-w-5xl mx-auto px-4 sm:px-8 py-28 pb-20">
      <header class="mb-10">
        <h1 class="font-display font-black text-3xl tracking-tight">Minha Conta</h1>
        <p class="text-on-surface-variant text-sm mt-1">
          Histórico de visualizações, comentários e perfil de leitor
        </p>
      </header>

      <!-- Perfil -->
      <section
        class="relative overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 md:p-8 shadow-2xl mb-8"
      >
        <div
          class="absolute -top-20 -right-20 w-56 h-56 bg-primary/20 rounded-full blur-[100px] pointer-events-none"
        ></div>
        <div class="relative flex items-center gap-5">
          <img
            v-if="usuario?.avatar"
            :src="usuario.avatar"
            alt="Foto de perfil"
            class="w-16 h-16 rounded-full object-cover border border-outline-variant/30"
          />
          <div
            v-else
            class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center text-primary font-black text-xl"
          >
            {{ inicial }}
          </div>
          <div class="min-w-0">
            <h2 class="text-xl font-bold truncate">{{ usuario?.name }}</h2>
            <p class="text-sm text-on-surface-variant truncate">{{ usuario?.email }}</p>
            <span
              class="mt-2 inline-block rounded-full bg-primary/20 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-primary"
            >
              Leitor
            </span>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Histórico de visualizações -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
        >
          <h3
            class="font-headline text-lg font-bold mb-5 flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-primary">history</span>
            Histórico de Visualizações
          </h3>
          <p v-if="visualizacoes.length === 0" class="text-sm text-on-surface-variant">
            Você ainda não visualizou nenhum post.
          </p>
          <ul v-else class="space-y-4">
            <li
              v-for="visualizacao in visualizacoes"
              :key="visualizacao.id"
              class="flex items-center justify-between gap-3"
            >
              <Link
                :href="url_do_post(visualizacao.post)"
                class="text-sm font-medium text-on-surface hover:text-primary transition-colors truncate no-underline"
              >
                {{ visualizacao.post.title }}
              </Link>
              <span class="text-xs font-mono text-on-surface-variant shrink-0">
                {{ data_formatada(visualizacao.viewed_at) }}
              </span>
            </li>
          </ul>
        </section>

        <!-- Meus comentários -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
        >
          <h3 class="font-headline text-lg font-bold mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">forum</span>
            Meus Comentários
          </h3>
          <p v-if="comentarios.length === 0" class="text-sm text-on-surface-variant">
            Você ainda não comentou em nenhum post.
          </p>
          <ul v-else class="space-y-4">
            <li v-for="comentario in comentarios" :key="comentario.id" class="space-y-1">
              <p class="text-sm text-slate-300 line-clamp-2">{{ comentario.content }}</p>
              <p class="text-xs">
                <Link
                  :href="url_do_post(comentario.post)"
                  class="text-primary font-bold no-underline hover:text-inverse-primary"
                >
                  {{ comentario.post.title }}
                </Link>
                <span class="font-mono text-on-surface-variant">
                  · {{ data_formatada(comentario.created_at) }}
                </span>
              </p>
            </li>
          </ul>
        </section>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";

const props = defineProps({
  visualizacoes: {
    type: Array,
    default: () => [],
  },
  comentarios: {
    type: Array,
    default: () => [],
  },
});

const usuario = computed(() => usePage().props.auth?.user ?? null);
const inicial = computed(() => (usuario.value?.name || "?").charAt(0).toUpperCase());

const url_do_post = (post) => `/post/show/${post.slug}`;

const data_formatada = (valor) => {
  if (!valor) {
    return "";
  }
  return DateTime.fromISO(valor).setLocale("pt-BR").toFormat("dd LLL, yyyy");
};
</script>
