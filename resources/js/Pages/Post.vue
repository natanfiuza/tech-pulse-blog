<template>
    <Navbar />
    <div class="post-container">

        <Head>
            <title>{{ post.title }}</title>
            <meta name="description" :content="post.excerpt">
        </Head>

        <h1 class="post-title">{{ post.title }}</h1>
        <img :src="`${post.image}`" :alt="post.title" class="post-image" />

        <div class="post-meta">
            <span>Publicado em: {{ formattedDate }}</span>
            <span>Autor: NatanFiuza</span>
        </div>

        <div class="post-content" v-html="post.content"></div>
    </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';
import { Head } from '@inertiajs/inertia-vue3'; // Importa o componente Head
import { DateTime } from 'luxon'; // Formatação de datas
import Navbar from '@/Components/Navbar.vue'; // Ajuste o caminho, se necessário
import "../../css/home.css";

const props = defineProps({
    post: Object,
});

// Formatação de data (usando Luxon)
const formattedDate = computed(() => {
    if (!props.post.created_at) {
        return null;
    }
    return DateTime.fromISO(props.post.created_at).toLocaleString(DateTime.DATE_FULL);
});

</script>

<style scoped>
.post-container {
    max-width: 800px;
    margin: 0 auto;
    margin-top: 2.1rem;
    /* Centraliza o conteúdo */
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.post-title {
    font-size: 2.5rem;
    /* Aumenta o tamanho do título */
    margin-bottom: 1rem;
    color: #333;
    text-align: center;
    /* Centraliza o título */
}

.post-image {
    width: 100%;
    height: auto;
    margin-bottom: 1.5rem;
    /* Espaço abaixo da imagem */
    border-radius: 8px;
    /* Bordas arredondadas */
    aspect-ratio: 16 / 9;
    object-fit: cover;
}

.post-meta {
    display: flex;
    justify-content: space-between;
    /* Espaço entre data e autor */
    margin-bottom: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.post-content {
    line-height: 1.6;
    color: #444;
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
    /* Para código */
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

        margin-top: 3.8rem;

    }

    .post-title {
        font-size: 1.3rem;
    }
}
</style>
