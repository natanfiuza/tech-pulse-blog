<template>
    <nav class="navbar">
        <a href="index.html" class="logo">TechPulse</a>
        <ul class="nav-links">
            <li><a href="index.html">Início</a></li>
            <li><a href="#">Artigos</a></li>
            <li><a href="#">Categorias</a></li>
            <li><a href="#">Sobre</a></li>
            <li><a href="#">Contato</a></li>
            <li><a href="login.html">Login</a></li>
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
import $ from "jquery";
import { defineProps } from 'vue';
import { InertiaLink } from '@inertiajs/inertia-vue3'; //Importe o InertiaLink
const props = defineProps({
    posts: Array
});

</script>

<style scoped>
/* Estilos específicos desta página */
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
</style>
