<template>
  <Navbar />

  <div class="post-container">
    <Head>
      <title>{{ post.title }}</title>
      <meta name="description" :content="post.excerpt" />
    </Head>

    <h1 class="post-title">{{ post.title }}</h1>
    <img :src="`${post.image}`" :alt="post.title" class="post-image" />

    <div class="article-meta">
      <span
        >Por <span id="article-author">NatanFiuza</span> |
        <span id="article-date">{{ formattedDate }}</span></span
      >
      <span
        >Tempo de leitura: <span id="reader-time">{{ read_time }}</span> min</span
      >
    </div>

    <div class="post-content article-content" ref="postContent"></div>
  </div>
  <section class="author-bio">
    <img src="/assets/img/natanfiuza.jpeg" alt="Maria Silva" class="author-avatar" />
    <div>
      <h3>Nataniel Fiuza</h3>
      <p>
        Desenvolvedor sênior com vasta experiência em PHP, Laravel, JavaScript e Python.
        Especialista em banco de dados, com domínio de MariaDB e Microsoft SQL Server, ele
        é conhecido por sua habilidade em otimizar sistemas de alto desempenho e
        implementar boas práticas de programação, como Código Limpo e arquitetura SOLID.
      </p>
    </div>
  </section>
</template>

<script setup>
import { defineProps, computed, ref, onMounted, watch } from "vue";
import { Head } from "@inertiajs/inertia-vue3"; // Importa o componente Head
import { DateTime } from "luxon"; // Formatação de datas
import Navbar from "@/Components/Navbar.vue";
import "../../css/home.css";
import "../../css/article.css";
import { tempo_leitura } from "../helpers";
import MarkdownIt from "markdown-it";
import hljs from "highlight.js";
import "highlight.js/styles/github-dark.css";
//import "../../css/themes/dracula.css";

const props = defineProps({
  post: Object,
});

const read_time = tempo_leitura(props.post.content);

// Formatação de data (usando Luxon)
const formattedDate = computed(() => {
  if (!props.post.created_at) {
    return null;
  }
  return DateTime.fromISO(props.post.created_at).toLocaleString(DateTime.DATE_FULL);
});

const postContent = ref(null);

onMounted(() => {
  renderMarkdown();
});

watch(
  () => props.post,
  () => {
    renderMarkdown();
  },
  { deep: true }
);

const renderMarkdown = () => {
  if (!props.post.content || !postContent.value) {
    return;
  }

  const md = new MarkdownIt({
    html: true,
    linkify: true,
    typographer: true,
  });

  const rawHtml = md.render(props.post.content);
  postContent.value.innerHTML = rawHtml; // Insere o HTML

  // Aplica o Highlight.js
  postContent.value.querySelectorAll("pre code").forEach((block) => {
    hljs.highlightElement(block);
  });
};
</script>

<style scoped>
.post-container {
  max-width: 90%;
  margin: 0 auto;
  margin-top: 5.1rem;

  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  font-size: 1.1rem;
}

.post-title {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #333;
  text-align: center;
}

.post-image {
  width: 100%;
  height: 35rem;
  margin-bottom: 1.5rem;
  border-radius: 8px;
  aspect-ratio: 16 / 9;
  object-fit: cover;
}

.post-meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: #666;
  font-size: 0.9rem;
}

.post-content {
  line-height: 1.6;
  color: #444;
  text-align: justify;
  text-justify: inter-word;
}

/* Estilos adicionais para elementos dentro do conteúdo (opcional) */
.post-content p {
  margin-bottom: 1rem;
}

.post-content h2,
.post-content h3,
.post-content h4 {
  margin-top: 2rem;
  margin-bottom: 1rem;
}

.post-content a {
  color: #007bff;
  text-decoration: none;
}

.post-content a:hover {
  text-decoration: underline;
}

.post-content ul,
.post-content ol {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
}

.post-content blockquote {
  border-left: 4px solid #ddd;
  padding-left: 1rem;
  margin-left: 0;
  font-style: italic;
}

.post-content pre {
  background: #f4f4f4;
  padding: 1rem;
  overflow-x: auto;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.post-content code {
  font-family: "Courier New", Courier, monospace;
}

@media (max-width: 768px) {
  .post-container {
    max-width: 100%;
    margin: 0 auto;
    padding-top: 6.5rem;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 1.2rem;
  }

  .post-title {
    font-size: 1.3rem;
  }

  .post-image {
    width: 100%;
    height: 16rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    aspect-ratio: 16 / 9;
    object-fit: cover;
  }

  p {
    margin-top: 0.5rem;
    margin-bottom: 0.2rem;
  }

  li {
    margin-left: 0.1rem;
  }
}
</style>

<style>
/* Estilos para a tag <pre> (SEM scoped!) */
pre {
  overflow-x: auto;
  /* Adiciona rolagem horizontal quando necessário */
  padding: 0.6rem;
  border-radius: 4px;
  /* (Opcional) Arredonda os cantos */
  background-color: #f0f0f0;
  /*(Opcional) Cor de fundo. Mude de acordo com o tema do highlight*/
  margin-bottom: 1rem;
}
code {
  font-family: "Fira Code", serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
}
</style>
