<template>
  <div class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30 flex flex-col">
    <Head>
      <title>{{ author.name }} - TechPulse</title>
      <meta
        name="description"
        :content="author.bio || `Artigos e publicações de ${author.name} no TechPulse Blog.`"
      />
    </Head>

    <Navbar />

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-8 py-28 pb-20">
      <!-- HEADER DO AUTOR -->
      <section
        class="relative overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 sm:p-10 shadow-2xl mb-12"
      >
        <div
          class="absolute -top-24 -right-24 w-72 h-72 bg-primary/20 rounded-full blur-[120px] pointer-events-none"
        ></div>

        <div class="relative flex flex-col md:flex-row items-start md:items-center gap-8">
          <!-- Avatar -->
          <div class="relative shrink-0">
            <img
              v-if="author.avatar_url"
              :src="author.avatar_url"
              :alt="author.name"
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-surface-container-high shadow-xl block"
            />
            <div
              v-else
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-primary/20 flex items-center justify-center text-primary text-3xl font-black"
            >
              {{ author.name.charAt(0) }}
            </div>
          </div>

          <!-- Informações do Autor -->
          <div class="flex-1 min-w-0 space-y-3">
            <div class="flex flex-wrap items-center gap-3">
              <h1 class="text-2xl sm:text-3xl font-black font-display tracking-tight text-on-surface">
                {{ author.name }}
              </h1>
              <span class="text-xs font-mono text-primary bg-primary/10 border border-primary/20 px-2.5 py-1 rounded-full">
                @{{ author.username }}
              </span>
            </div>

            <!-- E-mail (se autorizado pelo autor) -->
            <div v-if="author.email" class="flex items-center gap-2 text-xs text-on-surface-variant font-mono">
              <span class="material-symbols-outlined text-[16px] text-primary">mail</span>
              <a :href="`mailto:${author.email}`" class="hover:text-primary transition-colors">
                {{ author.email }}
              </a>
            </div>

            <!-- Biografia -->
            <p v-if="author.bio" class="text-sm text-on-surface-variant leading-relaxed max-w-3xl">
              {{ author.bio }}
            </p>
            <p v-else class="text-xs text-on-surface-variant italic">
              Autor no TechPulse Blog.
            </p>

            <!-- Redes Sociais -->
            <div v-if="tem_redes_sociais" class="flex flex-wrap items-center gap-2 pt-2">
              <a
                v-for="(config, rede) in author.social_links"
                :key="rede"
                :href="config.url"
                target="_blank"
                rel="noopener noreferrer"
                :title="rotulo_rede(rede)"
                class="inline-flex items-center gap-1.5 rounded-lg border border-outline-variant/30 bg-surface-container px-3 py-1.5 text-xs font-medium text-on-surface hover:border-primary/50 hover:text-primary transition-all duration-200"
              >
                <span class="material-symbols-outlined text-sm text-primary">{{ icone_rede(rede) }}</span>
                <span>{{ rotulo_rede(rede) }}</span>
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- ARTIGOS PUBLICADOS -->
      <section class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-outline-variant/20 pb-4">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl">article</span>
            <h2 class="text-xl sm:text-2xl font-black font-display tracking-tight text-on-surface">
              Artigos Publicados
            </h2>
          </div>
          <span class="text-xs font-mono text-on-surface-variant">
            {{ total_posts }} {{ total_posts === 1 ? "artigo encontrado" : "artigos encontrados" }}
          </span>
        </div>

        <!-- Lista Vazia -->
        <div
          v-if="lista_posts.length === 0"
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-12 text-center text-on-surface-variant text-sm"
        >
          <span class="material-symbols-outlined text-4xl text-on-surface-variant/40 mb-3 block">
            auto_stories
          </span>
          Nenhum artigo publicado por este autor no momento.
        </div>

        <!-- Grid de Posts -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <PostCard v-for="post in lista_posts" :key="post.id" :post="post" />
        </div>

        <!-- PAGINAÇÃO -->
        <nav
          v-if="posts.links && posts.links.length > 3"
          aria-label="Navegação entre páginas"
          class="flex flex-wrap items-center justify-center gap-2 pt-8"
        >
          <template v-for="(link, index) in posts.links" :key="index">
            <span
              v-if="!link.url"
              class="px-3.5 py-2 rounded-lg text-xs font-medium text-on-surface-variant/40 border border-outline-variant/10 cursor-not-allowed select-none"
              v-html="limpar_rotulo_paginacao(link.label)"
            ></span>
            <Link
              v-else
              :href="link.url"
              preserve-scroll
              class="px-3.5 py-2 rounded-lg text-xs font-medium transition-all border"
              :class="
                link.active
                  ? 'bg-primary text-on-primary font-bold border-primary shadow-md'
                  : 'bg-surface-container text-on-surface border-outline-variant/20 hover:border-primary/40 hover:text-primary'
              "
              v-html="limpar_rotulo_paginacao(link.label)"
            ></Link>
          </template>
        </nav>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import PostCard from "@/Components/PostCard.vue";

const props = defineProps({
  author: {
    type: Object,
    required: true,
  },
  posts: {
    type: Object,
    required: true,
  },
});

const lista_posts = computed(() => {
  return props.posts?.data || [];
});

const total_posts = computed(() => {
  return props.posts?.total ?? lista_posts.value.length;
});

const tem_redes_sociais = computed(() => {
  return props.author.social_links && Object.keys(props.author.social_links).length > 0;
});

const rotulo_rede = (rede) => {
  const mapa = {
    github: "GitHub",
    twitter_x: "X / Twitter",
    linkedin: "LinkedIn",
    website: "Website",
    instagram: "Instagram",
  };
  return mapa[rede] || rede;
};

const icone_rede = (rede) => {
  const mapa = {
    github: "code",
    twitter_x: "tag",
    linkedin: "work",
    website: "language",
    instagram: "photo_camera",
  };
  return mapa[rede] || "link";
};

const limpar_rotulo_paginacao = (rotulo) => {
  if (!rotulo) return "";
  return rotulo
    .replace("&laquo; Anterior", "« Anterior")
    .replace("Próximo &raquo;", "Próximo »");
};
</script>

