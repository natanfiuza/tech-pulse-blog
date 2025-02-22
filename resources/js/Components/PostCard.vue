<template>
    <div class="post-card">
        <InertiaLink :href="'/post/show/'+post.slug">
            <img :src="`${post.image}`" :alt="post.title" />
        </InertiaLink>
        <InertiaLink :href="'/post/show/'+post.slug">
            <h3>{{ post.title }}</h3>
        </InertiaLink>
        <p>{{ post.excerpt }}</p>
        <InertiaLink :href="'/post/show/'+post.slug" class="">Leia Mais</InertiaLink>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { InertiaLink } from '@inertiajs/inertia-vue3';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});
</script>

<style scoped>
.post-card {
    width: 300px;
    /* Ou o tamanho que você preferir */
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    display: flex;
    /* Flexbox para layout */
    flex-direction: column;
    /* Empilha os elementos verticalmente */
}

.post-card:hover {
    transform: translateY(-5px);
}

.post-card img {
    width: 100%;
    height: auto;
    display: block;
    /* Remove espaço extra abaixo da imagem */
    aspect-ratio: 16 / 9;
    object-fit: cover;
}

.post-card h3 {
    font-size: 1.2rem;
    margin: 10px;
    color: #333;
    min-height: 2.4em;
    /* Garante que o título ocupe pelo menos duas linhas */
    /* Permite que o título quebre em várias linhas */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    /* Limita a 2 linhas */
    overflow: hidden;
}

.post-card p {
    font-size: 1rem;
    margin: 10px;
    color: #666;
    flex-grow: 1;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
}

.read-more-link {
    margin-top: auto;
    margin-left: 10px;
    margin-bottom: 10px;
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
}

.read-more-link:hover {
    background-color: #0056b3;
}
@media (max-width: 768px) {
    .post-card {
            width: auto;


    }
}
</style>
