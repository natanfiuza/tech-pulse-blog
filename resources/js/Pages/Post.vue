<template>
    <Navbar />

    <div class="post-container">

        <Head>
            <title>{{ post.title }}</title>
            <meta name="description" :content="post.excerpt">
        </Head>

        <h1 class="post-title">{{ post.title }}</h1>
        <img :src="`${post.image}`" :alt="post.title" class="post-image" />

        <div class="article-meta">
            <span>Por <span id="article-author">NatanFiuza</span> | <span id="article-date">{{ formattedDate
                    }}</span></span>
            <span>Tempo de leitura: <span id="reader-time">{{ read_time }}</span> min</span>
        </div>

        <div class="post-content article-content" v-html="post.content" v-copy-code>


        </div>
    </div>
    <section class="author-bio">
        <img src="/assets/img/natanfiuza.jpeg" alt="Maria Silva" class="author-avatar" />
        <div>
            <h3>Nataniel Fiuza</h3>
            <p>
                Desenvolvedor sênior com vasta experiência em PHP, Laravel,
                JavaScript e Python. Especialista em banco de dados, com domínio de
                MariaDB e Microsoft SQL Server, ele é conhecido por sua habilidade
                em otimizar sistemas de alto desempenho e implementar boas práticas
                de programação, como Código Limpo e arquitetura SOLID.
            </p>
        </div>
    </section>

</template>

<script setup>
import { defineProps, computed } from 'vue';
import { Head } from '@inertiajs/inertia-vue3'; // Importa o componente Head
import { DateTime } from 'luxon'; // Formatação de datas
import Navbar from '@/Components/Navbar.vue'; // Ajuste o caminho, se necessário
import "../../css/home.css";
import "../../css/article.css";
import { tempo_leitura } from "../helpers";

// Prism highlighting
// import { marked } from 'marked';
// import '../../css/themes/prism.css';
// import Prism from 'prismjs';


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
// Diretiva customizada v-copy-code
const copyCode = {
    mounted(el) { // Usa 'mounted' em vez de 'bind' (Vue 3)
        el.querySelectorAll('pre').forEach((preElement) => {
            const code = preElement.innerText; //Pega o texto de dentro do pre

            // Cria o botão
            const copyButton = document.createElement('button');
            copyButton.textContent = 'Copiar';
            copyButton.classList.add('copy-button'); // Adiciona uma classe para estilização

            // Adiciona o botão *antes* do <pre>
            preElement.parentNode.insertBefore(copyButton, preElement);

            // Adiciona o event listener ao botão
            copyButton.addEventListener('click', () => {
                navigator.clipboard.writeText(code).then(() => {
                    // Feedback visual (opcional)
                    copyButton.textContent = 'Copiado!';
                    setTimeout(() => {
                        copyButton.textContent = 'Copiar';
                    }, 2000); // Volta para "Copiar" depois de 2 segundos
                }).catch(err => {
                    console.error('Erro ao copiar: ', err);
                    // Mostrar mensagem de erro (opcional)
                });
            });
        });
    }
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
    font-family: 'Courier New', Courier, monospace;
}

@media (max-width: 768px) {
    .post-container {

        max-width: 100%;
        margin: 0 auto;
        padding-top: 5.7rem;
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
        margin-top: .5rem;
        margin-bottom: .2rem;
    }

    li {
        margin-left: .1rem;
    }
}
</style>

<style>
/* Estilos para a tag <pre> (SEM scoped!) */
pre {
    position: relative;
    overflow-x: auto;
    padding: .6rem;
    border-radius: 5px;
    background-color: #d6d3d3;
    margin-bottom: 1rem;
}
code {
    font-family: "Fira Code", serif;
    font-optical-sizing: auto;
    font-weight: 700;
    font-style: normal;
}
pre code.hljs {
    padding: 10px;
    display: block;
}

.copy-button {
    position: absolute;
    /* Posiciona o botão */
    top: 0.5rem;
    /* Distância do topo */
    right: 0.5rem;
    /* Distância da direita */
    background-color: #007bff;
    color: white;
    border: none;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    opacity: 0;
    /* Começa invisível */
    transition: opacity 0.2s ease;
    z-index: 50;
}

/* Mostra o botão no hover do <pre> */
pre:hover .copy-button {
    opacity: 1;
}

.post-content {
    line-height: 1.6;
    color: #444;
}
</style>
