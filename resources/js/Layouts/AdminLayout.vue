<template>
  <div class="min-h-screen bg-surface-dim text-on-surface font-body">
    <!-- Overlay (mobile): fecha a sidebar ao clicar fora -->
    <div
      v-if="sidebar_open"
      class="fixed inset-0 z-30 bg-black/60 md:hidden"
      aria-hidden="true"
      @click="sidebar_open = false"
    ></div>

    <Sidebar :open="sidebar_open" @close="sidebar_open = false" />
    <Topbar :breadcrumb="breadcrumb" @toggle-sidebar="sidebar_open = !sidebar_open" />

    <main class="min-h-screen pt-24 px-4 pb-12 md:ml-64 md:px-8">
      <slot />
    </main>
  </div>
</template>

<script>
import Topbar from "@/Components/Admin/Topbar.vue";
import Sidebar from "@/Components/Admin/Sidebar.vue";

const rotulos_breadcrumb = {
    "Admin/AdminHome": "Dashboard",
    "Admin/Posts/PostsIndex": "Posts",
    "Admin/Posts/PostsCreate": "Novo Post",
    "Admin/Posts/PostsEdit": "Editar Post",
    "Admin/Categories/CategoriesIndex": "Categorias",
    "Admin/Categories/CategoriesCreate": "Nova Categoria",
    "Admin/Categories/CategoriesEdit": "Editar Categoria",
};

export default {
    components: {
        Topbar,
        Sidebar,
    },
    data() {
        return {
            sidebar_open: false,
        };
    },
    computed: {
        breadcrumb() {
            return rotulos_breadcrumb[this.$page.component] ?? "Admin";
        },
    },
    created() {
        // Fecha a sidebar ao navegar (útil no mobile)
        this.$watch(
            () => this.$page.url,
            () => {
                this.sidebar_open = false;
            }
        );
    },
};
</script>
