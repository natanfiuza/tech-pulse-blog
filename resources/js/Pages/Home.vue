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
        <div class="post-card">
            <img src="/assets/img/artigo_card_1.jpeg" alt="Artigo em Destaque" />
            <h3>Inteligência Artificial: Revolução Tecnológica</h3>
            <p>Descubra como a IA está transformando diversos setores.</p>
            <a href="artigo_1.html">Leia Mais</a>
        </div>
        <div class="post-card">
            <img src="/assets/img/artigo_card_2.jpeg" alt="Artigo em Destaque" />
            <h3>Tendências em Desenvolvimento Web</h3>
            <p>As principais tecnologias que estão moldando o futuro da web.</p>
            <a href="artigo_5.html">Leia Mais</a>
        </div>
        <div class="post-card">
            <img src="/assets/img/artigo_card_3.jpeg" alt="Artigo em Destaque" />
            <h3>Cibersegurança em 2024</h3>
            <p>Estratégias essenciais para proteger seus dados digitais.</p>
            <a href="artigo_6.html">Leia Mais</a>
        </div>
        <div class="post-card">
            <img src="/assets/img/artigo_machine_learning_fundamentos.png" alt="Artigo em Destaque" />
            <h3>Machine Learning: Fundamentos</h3>
            <p>Desvende os princípios fundamentais do machine learning e prepare o terreno para aplicações avançadas.
            </p>
            <a href="artigo_2.html">Leia Mais</a>
        </div>
        <div class="post-card">
            <img src="/assets/img/artigo_etica_inteligencia_artificial.jpeg" alt="Artigo em Destaque" />
            <h3>Ética na Inteligência Artificial</h3>
            <p>Quais são os dilemas morais e as implicações transformadoras da Ética na Inteligência Artificial.</p>
            <a href="artigo_3.html">Leia Mais</a>
        </div>
        <div class="post-card">
            <img src="/assets/img/ia_mercado_financeiro.jpeg" alt="Artigo em Destaque" />
            <h3>IA no Mercado Financeiro</h3>
            <p>A IA está revolucionando o mercado financeiro, desde a negociação algorítmica até a detecção de fraudes.
            </p>
            <a href="artigo_4.html">Leia Mais</a>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { InertiaLink } from '@inertiajs/inertia-vue3'; // Importe o InertiaLink
import "../../css/home.css"; 
import Navbar from '@/Components/Navbar.vue'; // Ajuste o caminho, se necessário

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
