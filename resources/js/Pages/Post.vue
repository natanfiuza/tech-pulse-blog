<template>
  <div
    class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30 flex flex-col"
  >
    <Head>
      <title>TechPulse - {{ post.title }}</title>
      <meta name="description" :content="post.excerpt" />
    </Head>

    <Navbar />

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-8 py-12 lg:grid lg:grid-cols-12 lg:gap-12">
      <div class="lg:col-span-8 min-w-0">
        <!-- Cabeçalho do artigo -->
        <header class="mb-10 relative">
          <div
            class="absolute -top-24 -left-24 w-64 h-64 bg-primary/20 rounded-full blur-[100px] pointer-events-none"
          ></div>

          <div class="flex flex-wrap items-center gap-3 mb-6 relative">
            <CategoryChip v-if="post.category" :category="post.category" />
            <HashtagChip
              v-for="hashtag in post.hashtags || []"
              :key="hashtag.id"
              :hashtag="hashtag"
            />
          </div>

          <h1
            class="text-3xl sm:text-5xl lg:text-[56px] font-black leading-tight tracking-tight mb-8 relative"
          >
            {{ post.title }}
          </h1>

          <div
            class="flex flex-wrap items-center gap-x-4 gap-y-3 text-on-surface-variant font-medium text-sm relative"
          >
            <div class="flex items-center gap-3">
              <Link v-if="url_perfil_autor" :href="url_perfil_autor" class="shrink-0">
                <img
                  v-if="avatar_autor"
                  :src="avatar_autor"
                  :alt="nome_autor"
                  class="w-12 h-12 rounded-full border-2 border-surface-container-high object-cover block hover:border-primary/50 transition-colors"
                />
                <div
                  v-else
                  class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-lg"
                >
                  {{ nome_autor.charAt(0) }}
                </div>
              </Link>
              <template v-else>
                <img
                  v-if="avatar_autor"
                  :src="avatar_autor"
                  :alt="nome_autor"
                  class="w-12 h-12 rounded-full border-2 border-surface-container-high object-cover block"
                />
                <div
                  v-else
                  class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-lg"
                >
                  {{ nome_autor.charAt(0) }}
                </div>
              </template>
              <div class="flex flex-col">
                <Link
                  v-if="url_perfil_autor"
                  :href="url_perfil_autor"
                  class="text-slate-900 dark:text-white font-bold hover:text-primary transition-colors"
                >
                  {{ nome_autor }}
                </Link>
                <span v-else class="text-slate-900 dark:text-white font-bold">{{ nome_autor }}</span>
                <span class="text-slate-500 dark:text-on-surface-variant">Autor</span>
              </div>
            </div>
            <div class="h-8 w-px bg-slate-300 dark:bg-outline-variant"></div>
            <div class="flex flex-col">
              <span>{{ data_formatada }}</span>
              <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">schedule</span>
                {{ read_time }} min de leitura
              </span>
            </div>
          </div>
        </header>

        <!-- Imagem de destaque -->
        <figure v-if="imagem_url" class="mb-12">
          <img
            :src="imagem_url"
            :alt="post.title"
            class="w-full aspect-video object-cover rounded-xl shadow-2xl block"
          />
        </figure>

        <!-- Conteúdo -->
        <article
          class="article-content"
          ref="postContentContainer"
        ></article>

        <!-- Caixa de Autor -->
        <AuthorBox :user="post.user" />

        <!-- Discussão -->
        <CommentSection
          :comments="post.comments || []"
          :post_id="post.id"
          class="mt-12"
        />
      </div>

      <!-- Sidebar -->
      <aside
        class="lg:col-span-4 space-y-8 mt-12 lg:mt-0"
        aria-label="Conteúdo complementar"
      >
        <SidebarPanel titulo="Tags Relacionadas" icone="sell">
          <div class="flex flex-wrap gap-2">
            <HashtagChip
              v-for="hashtag in post.hashtags || []"
              :key="hashtag.id"
              :hashtag="hashtag"
            />
          </div>
        </SidebarPanel>

        <SidebarPanel id="newsletter" titulo="Boletim Informativo">
          <p class="text-xs text-on-surface-variant mb-4 leading-relaxed">
            Receba as últimas tendências de tecnologia diretamente no seu e-mail.
          </p>
          <form class="space-y-3" @submit.prevent="inscrever_newsletter">
            <label for="newsletter_email" class="sr-only">Seu e-mail</label>
            <input
              id="newsletter_email"
              v-model="email_newsletter"
              type="email"
              required
              placeholder="seu@email.com"
              class="w-full bg-white dark:bg-surface border border-slate-300 dark:border-outline-variant/50 rounded-lg px-4 py-2 text-sm text-slate-900 dark:text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-on-surface-variant/50"
            />
            <button
              type="submit"
              class="w-full bg-primary text-white py-2 rounded-lg font-bold text-sm glow-hover transition-all"
            >
              {{ inscrito ? "Inscrito!" : "Inscrever" }}
            </button>
          </form>
        </SidebarPanel>
      </aside>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch, nextTick } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import MarkdownIt from "markdown-it";
import hljs from "highlight.js";
import "highlight.js/styles/night-owl.css";
import mermaid from "mermaid";
import { normalizar_origem_conteudo, tempo_leitura, url_da_imagem } from "@/helpers";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import CategoryChip from "@/Components/CategoryChip.vue";
import HashtagChip from "@/Components/HashtagChip.vue";
import SidebarPanel from "@/Components/SidebarPanel.vue";
import CommentSection from "@/Components/CommentSection.vue";
import AuthorBox from "@/Components/AuthorBox.vue";

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

// Domínio de produção gravado nas URLs das imagens de conteúdo (prop
// compartilhada pelo HandleInertiaRequests).
const page = usePage();
const techpulse_url_publica = computed(() => page.props.techpulse_url_publica || "");

// --- Configuração do markdown-it com suporte a mermaid ---
const md = new MarkdownIt({
  html: true,
  linkify: true,
  typographer: true,
});

const defaultFenceRenderer =
  md.renderer.rules.fence ||
  function (tokens, idx, options, env, self) {
    return `<pre><code ${self.renderAttrs(tokens[idx])}>${md.utils.escapeHtml(
      tokens[idx].content
    )}</code></pre>`;
  };

md.renderer.rules.fence = (tokens, idx, options, env, self) => {
  const token = tokens[idx];
  const info = token.info ? token.info.trim() : "";

  if (info === "mermaid") {
    return `<pre class="mermaid">${token.content}</pre>`;
  }

  return defaultFenceRenderer(tokens, idx, options, env, self);
};

// As imagens de conteúdo são gravadas com a URL absoluta de produção; o
// navegador troca a origem na renderização. Feito na regra do token (e não no
// markdown antes de renderizar) para não mexer em URLs dentro de code blocks.
const regra_imagem_padrao =
  md.renderer.rules.image ||
  function (tokens, idx, options, env, self) {
    return self.renderToken(tokens, idx, options);
  };

md.renderer.rules.image = (tokens, idx, options, env, self) => {
  const token = tokens[idx];
  const src = token.attrGet("src");

  if (src) {
    token.attrSet("src", normalizar_origem_conteudo(src, techpulse_url_publica.value));
  }

  return regra_imagem_padrao(tokens, idx, options, env, self);
};

// --- Propriedades derivadas ---
const nome_autor = computed(() => {
  if (!props.post?.user) return "Usuário removido";
  const profile = props.post.user.profile;
  if (profile && profile.show_name === false && profile.username) {
    return profile.username;
  }
  return props.post.user.name || "Autor";
});

const avatar_autor = computed(() => {
  return props.post?.user?.avatar_url || props.post?.user?.avatar || "";
});

const url_perfil_autor = computed(() => {
  const profile = props.post?.user?.profile;
  if (profile && profile.public_profile_enabled !== false && profile.username) {
    return `/${profile.username}`;
  }
  return null;
});

const read_time = computed(() => {
  return props.post?.content ? tempo_leitura(props.post.content) : 1;
});

const data_formatada = computed(() => {
  if (!props.post?.created_at) {
    return "";
  }
  return DateTime.fromISO(props.post.created_at)
    .setLocale("pt-BR")
    .toLocaleString(DateTime.DATE_FULL);
});

const imagem_url = computed(() => url_da_imagem(props.post));

// --- Newsletter (estado local; backend na Fase 4) ---
const email_newsletter = ref("");
const inscrito = ref(false);

const inscrever_newsletter = () => {
  inscrito.value = true;
};

// --- Renderização do conteúdo ---
const postContentContainer = ref(null);

const renderContent = async () => {
  if (!props.post?.content || !postContentContainer.value) {
    if (postContentContainer.value) {
      postContentContainer.value.innerHTML = "";
    }
    return;
  }

  const rawHtml = md.render(props.post.content);
  postContentContainer.value.innerHTML = rawHtml;

  await nextTick();

  try {
    const blocksToHighlight = postContentContainer.value.querySelectorAll(
      "pre code:not(pre.mermaid code)"
    );
    blocksToHighlight.forEach((block) => {
      hljs.highlightElement(block);
    });
  } catch (error) {
    // Mantém o bloco sem destaque; falha de highlight não interrompe a leitura.
  }

  try {
    const mermaidElements = postContentContainer.value.querySelectorAll("pre.mermaid");
    if (mermaidElements.length > 0) {
      await mermaid.run({ nodes: mermaidElements });
    }
  } catch (error) {
    // Diagrama com sintaxe inválida permanece visível como texto do bloco.
  }
};

onMounted(() => {
  mermaid.initialize({
    startOnLoad: false,
    theme: "default",
  });
  renderContent();
});

watch(
  () => props.post,
  () => {
    renderContent();
  },
  { deep: true }
);
</script>
