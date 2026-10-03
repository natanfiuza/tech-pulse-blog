<template>
  <div
    class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30"
  >
    <Navbar :categorias="categorias" :categoria_ativa="categoria_ativa" />

    <main class="pt-24 md:pt-28 pb-20 px-4 sm:px-8 max-w-screen-2xl mx-auto">
      <!-- Destaque -->
      <section v-if="featured_post" class="mb-16">
        <Link
          :href="url_do_post(featured_post)"
          class="relative block w-full h-[420px] md:h-[600px] rounded-xl overflow-hidden group no-underline"
        >
          <img
            v-if="imagem_do_post(featured_post)"
            :src="imagem_do_post(featured_post)"
            :alt="featured_post.title"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 block"
            @load="ao_carregar_imagem_destaque"
          />
          <div
            v-else
            class="w-full h-full bg-gradient-to-br from-primary/30 to-surface-container-high flex items-center justify-center"
          >
            <span class="material-symbols-outlined text-8xl text-primary/40">code</span>
          </div>
          <div
            class="absolute inset-0 transition-colors duration-500"
            :class="overlay_gradiente_classe"
          ></div>
          <div class="absolute bottom-0 left-0 p-6 md:p-12 max-w-4xl">
            <span
              class="bg-primary px-3 py-1 rounded text-[10px] font-black tracking-widest uppercase mb-4 inline-block text-white"
            >
              Destaque
            </span>
            <h1
              :class="titulo_destaque_classe"
              class="text-3xl sm:text-5xl md:text-6xl font-black leading-tight tracking-tight mb-4 md:mb-6 transition-all duration-300"
            >
              {{ featured_post.title }}
            </h1>
            <p
              :class="resumo_destaque_classe"
              class="text-base md:text-lg max-w-2xl mb-6 md:mb-8 leading-relaxed line-clamp-3 transition-all duration-300"
            >
              {{ featured_post.excerpt }}
            </p>
            <div class="flex flex-wrap items-center gap-4">
              <span
                class="bg-primary text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-extrabold flex items-center gap-2 glow-hover transition-all"
              >
                Ler Reportagem Completa
                <span class="material-symbols-outlined">arrow_forward</span>
              </span>
              <span
                :class="tempo_leitura_classe"
                class="text-sm font-mono transition-colors duration-300"
              >
                {{ tempo_leitura_do_post(featured_post) }} min de leitura
              </span>
            </div>
          </div>
        </Link>
      </section>

      <!-- Conteúdo + Sidebar -->
      <section class="flex flex-col lg:flex-row gap-12">
        <div class="flex-1 min-w-0">
          <!-- Chips de categoria -->
          <div class="flex flex-wrap items-center gap-3 mb-12">
            <Link
              href="/"
              :class="chip_classe(categoria_ativa === null)"
              class="px-5 py-2 rounded-full text-xs font-bold tracking-wider uppercase no-underline transition-colors"
            >
              Tudo
            </Link>
            <Link
              v-for="categoria in categorias"
              :key="categoria.id"
              :href="route('home', { categoria: categoria.slug })"
              :class="chip_classe(categoria_ativa === categoria.slug)"
              class="px-5 py-2 rounded-full text-xs font-bold tracking-wider uppercase no-underline transition-colors"
            >
              {{ categoria.name }}
            </Link>
          </div>

          <!-- Grade de posts -->
          <p v-if="posts_grade.length === 0" class="text-on-surface-variant text-sm">
            Nenhum post publicado nesta categoria ainda.
          </p>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <PostCard v-for="post in posts_grade" :key="post.id" :post="post" />
          </div>
        </div>

        <aside class="w-full lg:w-96 space-y-12" aria-label="Conteúdo complementar">
          <!-- Newsletter -->
          <SidebarPanel id="newsletter" titulo="Boletim Informativo">
            <NewsletterBox current_lang="pt_br" />
          </SidebarPanel>

          <!-- Tags populares -->
          <SidebarPanel sem_moldura titulo="Tags Populares" icone="trending_up">
            <div class="flex flex-wrap gap-2">
              <HashtagChip
                v-for="hashtag in tags_populares"
                :key="hashtag.slug"
                :hashtag="hashtag"
              />
              <p
                v-if="tags_populares.length === 0"
                class="text-sm text-on-surface-variant"
              >
                Nenhuma tag registrada ainda.
              </p>
            </div>
          </SidebarPanel>

          <!-- Autor -->
          <div
            class="relative overflow-hidden rounded-xl bg-gradient-to-br from-primary/20 to-white dark:to-surface-container-high p-6 md:p-8 border border-primary/20"
          >
            <div class="relative z-10">
              <h4 class="text-xl font-bold mb-4 leading-tight">
                Torne-se um Autor no TechPulse
              </h4>
              <p class="text-sm text-on-surface-variant mb-6">
                Compartilhe seu conhecimento com desenvolvedores de todo o Brasil.
              </p>
              <Link
                href="/tornar-se-autor"
                class="inline-flex items-center gap-2 text-primary text-sm font-black uppercase tracking-widest hover:translate-x-1 transition-transform no-underline"
              >
                Saiba como participar
                <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
              </Link>
            </div>
            <span
              class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl text-primary/10 rotate-12"
            >
              edit_note
            </span>
          </div>
        </aside>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import { tempo_leitura, url_da_imagem, analisar_contraste_imagem } from "@/helpers";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import PostCard from "@/Components/PostCard.vue";
import SidebarPanel from "@/Components/SidebarPanel.vue";
import HashtagChip from "@/Components/HashtagChip.vue";
import NewsletterBox from "@/Components/NewsletterBox.vue";

const props = defineProps({
  posts: {
    type: Array,
    default: () => [],
  },
  categorias: {
    type: Array,
    default: () => [],
  },
  categoria_ativa: {
    type: String,
    default: null,
  },
});

const featured_post = computed(() => props.posts.find((post) => post.featured_post));

const destaque_eh_escuro = ref(true);

const atualizar_contraste_destaque = async (url) => {
  if (!url) {
    destaque_eh_escuro.value = true;
    return;
  }
  const resultado = await analisar_contraste_imagem(url, { amostra_inicio_y: 0.35 });
  destaque_eh_escuro.value = resultado.eh_escura;
};

const ao_carregar_imagem_destaque = (evento) => {
  if (evento?.target) {
    analisar_contraste_imagem(evento.target, { amostra_inicio_y: 0.35 }).then(
      (resultado) => {
        destaque_eh_escuro.value = resultado.eh_escura;
      }
    );
  }
};

watch(
  () => (featured_post.value ? imagem_do_post(featured_post.value) : null),
  (nova_url) => {
    if (nova_url) {
      atualizar_contraste_destaque(nova_url);
    }
  },
  { immediate: true }
);

const titulo_destaque_classe = computed(() => {
  return destaque_eh_escuro.value
    ? "text-white [text-shadow:0_2px_10px_rgba(0,0,0,0.85)]"
    : "text-zinc-950 [text-shadow:0_2px_8px_rgba(255,255,255,0.9)]";
});

const resumo_destaque_classe = computed(() => {
  return destaque_eh_escuro.value
    ? "text-zinc-200 [text-shadow:0_0_12px_rgba(0,0,0,0.9),0_2px_8px_rgba(0,0,0,0.8)]"
    : "text-zinc-800 [text-shadow:0_0_12px_rgba(255,255,255,0.95),0_2px_8px_rgba(255,255,255,0.85)]";
});

const tempo_leitura_classe = computed(() => {
  return destaque_eh_escuro.value
    ? "text-zinc-300 [text-shadow:0_1px_4px_rgba(0,0,0,0.8)]"
    : "text-zinc-700 [text-shadow:0_1px_4px_rgba(255,255,255,0.8)]";
});

const overlay_gradiente_classe = computed(() => {
  return destaque_eh_escuro.value
    ? "bg-gradient-to-t from-black/80 via-black/40 to-transparent"
    : "bg-gradient-to-t from-white/80 via-white/40 to-transparent";
});

const posts_grade = computed(() => props.posts.filter((post) => !post.featured_post));

const tags_populares = computed(() => {
  const mapa = new Map();
  props.posts.forEach((post) => {
    (post.hashtags || []).forEach((hashtag) => {
      const atual = mapa.get(hashtag.slug);
      mapa.set(hashtag.slug, {
        ...hashtag,
        count: (atual ? atual.count : 0) + 1,
      });
    });
  });
  return [...mapa.values()].sort((a, b) => b.count - a.count).slice(0, 10);
});

const chip_classe = (ativa) => {
  if (ativa) {
    return "bg-primary text-white";
  }
  return "bg-surface-container-high text-on-surface-variant hover:text-white border border-outline-variant/20";
};

const url_do_post = (post) => `/post/show/${post.slug}`;

const imagem_do_post = (post) => url_da_imagem(post);

const tempo_leitura_do_post = (post) => {
  const texto = post.content || post.excerpt || "";
  return tempo_leitura(texto);
};
</script>
