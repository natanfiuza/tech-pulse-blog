<template>
    <nav class="navbar">
        <a href="/" class="logo">TechPulse</a>
        <div class="menu-toggle" id="mobile-menu" @click="toggleMenu" :class="{ active: isMenuActive }">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul class="nav-links" :class="{ active: isMenuActive }">
            <li><a href="/" @click.prevent="closeMenu">Início</a></li>
            <li><a href="#" @click.prevent="closeMenu">Artigos</a></li>
            <li><a href="#" @click.prevent="closeMenu">Categorias</a></li>
            <li><a href="#" @click.prevent="closeMenu">Sobre</a></li>
            <li><a href="#" @click.prevent="closeMenu">Contato</a></li>
            <li><a href="/login" @click.prevent="closeMenu">Login</a></li>
        </ul>
    </nav>

    <section class="hero">
        <h1>Descubra o Futuro da Tecnologia</h1>
        <p>
            Explore os últimos insights, tendências e inovações no mundo da
            tecnologia. Nosso blog traz análises profundas e conteúdo de qualidade
            para entusiastas e profissionais.
        </p>
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
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { InertiaLink } from '@inertiajs/inertia-vue3'; // Importe o InertiaLink
import "../../css/home.css"; // Certifique-se de que o caminho está correto

const props = defineProps({
    posts: Array,
});

// Use ref() para criar variáveis reativas
const isMenuActive = ref(false);

const toggleMenu = () => {
    isMenuActive.value = !isMenuActive.value;
};

const closeMenu = () => {
    isMenuActive.value = false;
};

const handleClickOutside = (event) => {
    const navbar = document.querySelector('.navbar'); // Usar querySelector, já que $el não está disponível diretamente no setup.
    if (!navbar.contains(event.target) && isMenuActive.value) {
        closeMenu();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>

</style>
