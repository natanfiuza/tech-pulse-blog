<template>
  <aside :class="{ sidebar: true, 'sidebar-open': showSidebar || !isMobile }">
    <button
      v-if="isMobile"
      @click="toggleSidebar"
      class="hamburger-button"
      aria-label="Alternar menu"
    >
      ☰
    </button>
    <ul>
      <li>
        <Link href="/admin/posts">Posts</Link>
      </li>
      <li>
        <Link href="/admin/categories">Categorias</Link>
      </li>
      <li>
        <Link href="/admin/users">Usuários</Link>
      </li>
    </ul>
  </aside>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3"; // Importante!
export default {
  components: { Link },
  data() {
    return {
      showSidebar: false,
      isMobile: false,
    };
  },
  mounted() {
    this.checkIsMobile();
    window.addEventListener("resize", this.checkIsMobile);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkIsMobile);
  },
  methods: {
    toggleSidebar() {
      this.showSidebar = !this.showSidebar;
    },
    checkIsMobile() {
      this.isMobile = window.innerWidth <= 768; // Ajuste o breakpoint conforme necessário
      if (!this.isMobile) {
        this.showSidebar = false; // Garante que o menu fique fechado em telas maiores
      }
    },
  },
};
</script>

<style scoped>
.sidebar {
  margin-top: -5px;
  width: 25vh;
  /* Largura da sidebar */
  background-color: #2d4363;
  /* Cor de fundo */
  color: white;
  /* Cor do texto */
  transition: transform 0.3s ease;
  /* Animação suave */
}

.sidebar ul {
  list-style: none;
  /* Remove marcadores da lista */
  padding: 0;
  margin: 0;
}

.sidebar li {
  padding: 10px 15px;
  /* Espaçamento interno */
}

.sidebar a {
  color: white;
  text-decoration: none;
  /* Remove sublinhado */
  display: block;
  /* Faz o link ocupar a largura total do item */
}

.sidebar a:hover {
  background-color: #555;
  /* Cor de fundo ao passar o mouse */
}

/* Estilos para mobile */
.hamburger-button {
  display: none;
  /* Escondido por padrão */
  background-color: #555;
  color: rgb(81, 57, 189);
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  position: fixed;
  /*Fica fixo no topo, mesmo com rolagem*/
  top: 10px;
  left: 10px;
  z-index: 100;
  /*Garante que o botão fique acima de outros elementos*/
}

@media (max-width: 768px) {
  /* Breakpoint para mobile */
  .hamburger-button {
    display: block;
    /* Mostra o botão em telas menores */
  }

  .sidebar {
    transform: translateX(-100%);
    /* Esconde a sidebar por padrão */
    position: fixed;
    /* Sidebar fixa para sobrepor o conteúdo */
    top: 10px;
    left: 0;
    height: 100vh;
    /* Ocupa a altura total */
    z-index: 99;
    /* Sidebar fica abaixo do botão hamburguer*/
  }

  .sidebar-open {
    transform: translateX(0);
    /* Mostra a sidebar */
  }
}
</style>
