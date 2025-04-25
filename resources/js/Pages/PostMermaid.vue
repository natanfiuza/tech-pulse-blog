<template>
  <Navbar />

  <div class="post-container">
    <Head>
      <title>{{ post.title }}</title>
      <meta name="description" :content="post.excerpt" />
    </Head>

    <h1 class="post-title">{{ post.title }}</h1>
    <img :src="`/storage/images/${post.uuid}`" :alt="post.title" class="post-image" />

    <div class="article-meta">
      <span
        >Por <span id="article-author">NatanFiuza</span> |
        <span id="article-date">{{ formattedDate }}</span></span
      >
      <span
        >Leitura: <span id="reader-time">{{ read_time }}</span> min</span
      >
    </div>

    <div class="post-content article-content" ref="postContentContainer"></div>
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
// --- Imports ---
import { defineProps, computed, ref, onMounted, watch, nextTick } from "vue"; // Adicionado nextTick
import { Head } from "@inertiajs/vue3"; // Corrigido para @inertiajs/vue3
import { DateTime } from "luxon";
import Navbar from "@/Components/Navbar.vue"; // Verifique o caminho
import "../../css/app.css";
import "../../css/home.css";
import "../../css/article.css";
import { tempo_leitura } from "../helpers"; // Verifique o caminho
import MarkdownIt from "markdown-it";
import hljs from "highlight.js";
import "highlight.js/styles/github-dark.css"; // Estilo para highlight.js
import mermaid from "mermaid"; // Importa Mermaid
// Opcional: Segurança para v-html/innerHTML
// import DOMPurify from 'dompurify';

// --- Props ---
const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

// --- Configuração do Markdown-it (Instanciado uma vez) ---
const md = new MarkdownIt({
  html: true, // ATENÇÃO: Permite HTML. Risco de XSS se a fonte não for confiável.
  linkify: true,
  typographer: true,
  // Opcional: Configurar highlight.js diretamente no markdown-it
  // highlight: function (str, lang) { ... }
});

// --- Customização do Markdown-it para Mermaid ---
const defaultFenceRenderer =
  md.renderer.rules.fence ||
  function (tokens, idx, options, env, self) {
    // Fallback para renderização padrão se a regra 'fence' não existir
    return `<pre><code ${self.renderAttrs(tokens[idx])}>${md.utils.escapeHtml(
      tokens[idx].content
    )}</code></pre>`;
  };

md.renderer.rules.fence = (tokens, idx, options, env, self) => {
  const token = tokens[idx];
  const info = token.info ? token.info.trim() : ""; // Linguagem (mermaid, php, js, etc.)

  if (info === "mermaid") {
    // Para blocos mermaid: renderiza <pre class="mermaid"> sem escapar o conteúdo interno
    return `<pre class="mermaid">${token.content}</pre>`;
  }

  // Para outros blocos de código: usa o renderizador padrão.
  // Se você NÃO configurou a opção `highlight` no new MarkdownIt,
  // o highlight.js será aplicado manualmente depois (Passo 5 na função renderContent).
  return defaultFenceRenderer(tokens, idx, options, env, self);
};
// --- Fim da Customização Mermaid ---

// --- Computed Properties ---
// Calcula o tempo de leitura quando o post.content muda
const read_time = computed(() => {
  // Verifica se props.post e props.post.content existem
  return props.post?.content ? tempo_leitura(props.post.content) : "N/A";
});

// Formata a data quando post.created_at muda
const formattedDate = computed(() => {
  if (!props.post?.created_at) {
    return null;
  }
  // Define o locale para Português (Brasil)
  return DateTime.fromISO(props.post.created_at)
    .setLocale("pt-BR")
    .toLocaleString(DateTime.DATE_FULL);
});

// --- Refs ---
const postContentContainer = ref(null); // Ref para o container do conteúdo no template

// --- Funções ---
// Função principal para renderizar todo o conteúdo dinâmico
const renderContent = async () => {
  // Garante que temos o container e o conteúdo Markdown
  if (!props.post?.content || !postContentContainer.value) {
    if (postContentContainer.value) postContentContainer.value.innerHTML = ""; // Limpa se não tiver conteúdo
    return;
  }

  // 1. Renderiza Markdown para HTML usando a instância configurada do markdown-it
  let rawHtml = md.render(props.post.content);

  // 2. **SEGURANÇA (Opcional, mas MUITO recomendado se `html: true` está ativo):**
  // Descomente a linha abaixo e instale DOMPurify (npm install dompurify)
  // para limpar o HTML antes de injetá-lo, prevenindo ataques XSS.
  // rawHtml = DOMPurify.sanitize(rawHtml);

  // 3. Insere o HTML (potencialmente sanitizado) no DOM
  postContentContainer.value.innerHTML = rawHtml;

  // 4. Aguarda o Vue atualizar o DOM com o novo innerHTML
  await nextTick();

  // 5. Aplica Highlight.js aos blocos de código <pre><code> (exceto os de mermaid)
  // Faça isso apenas se você *não* configurou a opção `highlight` na inicialização do MarkdownIt
  try {
    // Seleciona 'pre code' que NÃO estão dentro de um 'pre.mermaid'
    const blocksToHighlight = postContentContainer.value.querySelectorAll(
      "pre code:not(pre.mermaid code)"
    );
    blocksToHighlight.forEach((block) => {
      hljs.highlightElement(block);
    });
  } catch (error) {
    console.error("Erro ao aplicar Highlight.js:", error);
  }

  // 6. Encontra e renderiza os diagramas Mermaid
  try {
    // Seleciona todos os <pre class="mermaid"> dentro do container
    const mermaidElements = postContentContainer.value.querySelectorAll("pre.mermaid");
    if (mermaidElements.length > 0) {
      // console.log(`Mermaid: Encontrados ${mermaidElements.length} diagramas para renderizar.`);
      // Usa mermaid.run() para renderizar os nós encontrados - mais eficiente
      await mermaid.run({ nodes: mermaidElements });
      // console.log("Mermaid: Renderização concluída.");
    }
  } catch (error) {
    // Captura e loga erros específicos do Mermaid
    console.error("Erro ao renderizar diagrama Mermaid:", error);
    // Opcional: Adicionar uma mensagem de erro visual no lugar do diagrama
  }
};

// --- Lifecycle Hooks ---
onMounted(() => {
  // Inicializa o Mermaid (apenas uma vez quando o componente é montado)
  mermaid.initialize({
    startOnLoad: false, // Não executa automaticamente ao carregar a página inteira
    theme: "default", // Escolha o tema: default, dark, forest, neutral
    // securityLevel: 'loose', // Pode ser necessário ajustar dependendo do conteúdo
  });
  // Renderiza o conteúdo inicial
  renderContent();
});

// Observa por mudanças no objeto 'post' (útil se o post puder ser trocado dinamicamente)
// O 'deep: true' garante que mudanças *dentro* do objeto post também disparem a atualização
watch(
  () => props.post,
  () => {
    renderContent();
  },
  { deep: true }
);
</script>

<style scoped>
.post-container {
  max-width: 80%;
  margin: 0 auto;
  margin-top: 5.1rem;

  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  font-size: 1.1rem;
}

.post-title {
  font-size: 2.2rem;
  margin-bottom: 1rem;
  color: #333;
  text-align: center;
}

.post-image {
  width: 100%;
  /* height: 12rem; */
  /* Removido para usar aspect-ratio */
  max-height: 12rem;
  /* Altura máxima opcional */
  display: block;
  /* Para remover espaço extra abaixo */
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 1.5rem;
  border-radius: 8px;
  aspect-ratio: 16 / 9;
  /* Proporção da imagem */
  object-fit: cover;
  /* Como a imagem deve preencher a área */
}

.article-meta {
  display: flex;
  flex-wrap: wrap;
  /* Permite quebrar linha em telas pequenas */
  justify-content: space-between;
  gap: 0.5rem;
  /* Espaço entre os itens */
  margin-bottom: 1.7rem;
  color: #666;
  font-size: 0.8rem;
  /* Reduzido um pouco */
  border-bottom: 1px solid #eee;
  /* Separador visual */
  padding-bottom: 0.8rem;
}
.article-meta span {
  white-space: nowrap;
  /* Evita quebrar o texto de 'Leitura:' */
}
.post-content {
  line-height: 1.7;
  /* Aumentado para melhor legibilidade */
  color: #333;
  /* Escurecido um pouco */
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
  font-family: "Fira Code", serif;
}
.article-meta {
  display: flex;
  margin-bottom: 1.7rem;
  flex-direction: row;
  flex-wrap: wrap;
  align-content: center;
  justify-content: space-between;
  align-items: stretch;
  font-size: 0.7rem;
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
    height: 10rem;
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
  .article-meta {
    font-size: 0.7rem;
  }
}
</style>

<style>
code {
  font-family: "Fira Code", serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
}
/* Estilos Globais (SEM scoped) para conteúdo injetado via innerHTML */
.article-content p {
  margin-bottom: 1rem;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6 {
  margin-top: 1.8em;
  /* Mais espaço antes de títulos */
  margin-bottom: 0.8em;
  line-height: 1.3;
  font-weight: 600;
  /* Um pouco mais de peso */
}

.article-content h1 {
  font-size: 2em;
}

.article-content h2 {
  font-size: 1.6em;
}

.article-content h3 {
  font-size: 1.3em;
}

.article-content a {
  color: #0d6efd;
  /* Cor padrão do Bootstrap para links */
  text-decoration: none;
  transition: color 0.2s ease;
}

.article-content a:hover {
  color: #0a58ca;
  text-decoration: underline;
}

.article-content ul,
.article-content ol {
  margin-left: 1.5rem;
  margin-bottom: 1rem;
  padding-left: 1.5rem;
  /* Adiciona padding para alinhar melhor */
}

.article-content li {
  margin-bottom: 0.4rem;
  /* Espaço entre itens da lista */
}

.article-content blockquote {
  border-left: 4px solid #adb5bd;
  /* Cinza um pouco mais escuro */
  padding-left: 1rem;
  margin-left: 0;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
  font-style: italic;
  color: #495057;
  /* Cor do texto do blockquote */
}

.article-content blockquote p {
  margin-bottom: 0;
  /* Remove margem extra do parágrafo dentro */
}

/* Estilo base para pre (highlight.js usará .hljs internamente) */
.article-content pre {
  background: white;
  /* Fundo escuro para tema github-dark */
  padding: 1em;
  overflow-x: auto;
  border-radius: 6px;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  /* Sombra suave */
}

/* Estilo base para code inline e dentro de pre */
.article-content code {
  font-family: "Fira Code", Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
  font-size: 0.9em;
  /* Ligeiramente menor que o texto normal */
}

/* Código inline */
.article-content :not(pre) > code {
  background-color: #e9ecef;
  /* Fundo claro para código inline */
  padding: 0.2em 0.4em;
  border-radius: 3px;
  color: #343a40;
}

/* Estilos para Mermaid SVG (repetido do scoped para garantir) */
.article-content pre.mermaid > svg {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 1.5em auto;
}
</style>
