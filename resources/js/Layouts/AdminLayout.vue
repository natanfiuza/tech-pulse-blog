<template>
  <div class="admin-layout">
    <Topbar />
    <div class="content-wrapper">
      <Sidebar />
      <main class="main-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script>
import Topbar from "@/Components/Admin/Topbar.vue";
import Sidebar from "@/Components/Admin/Sidebar.vue";

export default {
  components: {
    Topbar,
    Sidebar,
  },
};
</script>

<style scoped>
/* Estilos básicos para o layout (adapte ao seu design) */
.admin-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content-wrapper {
  display: flex;
  flex-grow: 1;
}

.main-content {
  flex-grow: 1;
  padding: 20px;
}
</style>
