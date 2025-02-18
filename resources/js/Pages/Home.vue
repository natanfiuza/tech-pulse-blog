<template>
    <Navbar />
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Descubra o Futuro da Tecnologia</h1>
                <p>
                    Explore os últimos insights, tendências e inovações no mundo da
                    tecnologia. Nosso blog traz análises profundas e conteúdo de qualidade
                    para entusiastas e profissionais.
                </p>
            </div>

            <div v-if="featuredPost" class="hero-featured">
                <InertiaLink :href="'/post/show/'+ featuredPost.slug" class="featured-link">
                    <img :src="featuredPost.image" :alt="featuredPost.title" class="featured-image" />
                    <div class="featured-details">
                        <h2 class="featured-title">{{ featuredPost.title }}</h2>
                        <p class="featured-excerpt">{{ featuredPost.excerpt }}</p>
                    </div>
                </InertiaLink>
            </div>
        </div>
    </section>

    <section class="featured-posts" id="cards_home">
        <PostCard v-for="post in posts" :key="post.id" :post="post" />
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { InertiaLink } from '@inertiajs/inertia-vue3'; // Importe o InertiaLink
import "../../css/home.css";
import Navbar from '@/Components/Navbar.vue'; // Ajuste o caminho, se necessário
import PostCard from '@/Components/PostCard.vue'; // Importa o componente PostCard

const props = defineProps({
    posts: Array,
});


// Pega o primeiro post como destaque.
const featuredPost = computed(() => {
    let featured_post = null;
    props.posts.map((r) => {
        if (r.featured_post) {
            featured_post = r;
        }
    })

    return featured_post;
});

</script>

<style scoped>

</style>
