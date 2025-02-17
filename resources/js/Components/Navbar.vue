<template>
    <nav class="navbar">
        <a href="/" class="logo">TechPulse</a>
        <div class="menu-toggle" @click="toggleMenu" :class="{ active: isMenuActive }">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul class="nav-links" :class="{ active: isMenuActive }">
            <li>
                <InertiaLink href="/" @click="closeMenu">Início</InertiaLink>
            </li>
            <li>
                <InertiaLink href="#" @click="closeMenu">Artigos</InertiaLink>
            </li>
            <li>
                <InertiaLink href="#" @click="closeMenu">Categorias</InertiaLink>
            </li>
            <li>
                <InertiaLink href="#" @click="closeMenu">Sobre</InertiaLink>
            </li>
            <li>
                <InertiaLink href="#" @click="closeMenu">Contato</InertiaLink>
            </li>
            <li>
                <InertiaLink href="/login" @click="closeMenu">Login</InertiaLink>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { InertiaLink } from '@inertiajs/inertia-vue3'; // Importante!

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
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #001247;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    flex-direction: row;
    flex-wrap: wrap;
}

.nav-links {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    display: block;
    transition: background-color 0.3s ease;
}

.nav-links li {
    margin-left: 20px;
}

.nav-links a:hover {
    color: #6272b4;
}

.logo {
    font-size: 1.5rem;
    font-weight: bold;
    color: #fff;
    text-decoration: none;
}

/* Adicionar um botão "hambúrguer" (menu mobile) */
.menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 10px;
}

.menu-toggle span {
    height: 3px;
    width: 25px;
    background-color: white;
    margin: 3px 0;
    display: block;
    transition: 0.3s;
}

/* Media query para telas menores (ex: celulares) */
@media (max-width: 768px) {
    .navbar {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
    }

    .nav-links {
        display: none;
        width: 100%;
        flex-direction: column;
        text-align: center;
        position: absolute;
        top: 60px;
        left: 0;
        background-color: #001247;
        z-index: 1000;
    }

    .nav-links.active {
        display: flex;
    }

    .menu-toggle {
        display: flex;
    }

    /*Animação do Botão Hamburger*/
    .menu-toggle.active span:nth-child(1) {
        transform: translateY(9px) rotate(45deg);
    }

    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .menu-toggle.active span:nth-child(3) {
        transform: translateY(-9px) rotate(-45deg);
    }
}
</style>
