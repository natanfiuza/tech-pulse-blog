<template>
  <AdminLayout>
    <h2>Categorias</h2>

    <div v-if="$page.props.flash?.success" class="alert alert-success mb-4">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash?.error" class="alert alert-danger mb-4">
      {{ $page.props.flash.error }}
    </div>

    <Link
      :href="'/admin/categories/create'"
      class="btn btn-primary mb-3 btn-new-category"
    >
      Nova Categoria
    </Link>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Slug</th>
            <th>Categoria Pai</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>{{ category.id }}</td>
            <td>{{ category.name }}</td>
            <td>{{ category.slug }}</td>
            <td>{{ category.parent ? category.parent.name : "Nenhuma" }}</td>
            <td>
              <Link
                :href="route('categories.edit', { category: category.id })"
                class="btn btn-sm btn-info me-2"
              >
                Editar
              </Link>
              <!-- <button @click="confirmDelete(category)" class="btn btn-sm btn-danger">
                Excluir
              </button> -->
            </td>
          </tr>
          <tr v-if="categories.length === 0">
            <td colspan="5" class="text-center">Nenhuma categoria encontrada.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, usePage, router } from "@inertiajs/vue3"; // Use vue3 imports
import { defineProps } from "vue";

// Define as props recebidas do controller
const props = defineProps({
  categories: Array,
});

// Função para confirmar exclusão (melhor usar um modal em um app real)
const confirmDelete = (category) => {
  if (
    confirm(
      `Tem certeza que deseja excluir a categoria "${category.name}"? As subcategorias se tornarão categorias principais.`
    )
  ) {
    router.delete(route("categories.destroy", { category: category.id }), {
      preserveScroll: true, // Mantém a posição da página após a ação
      onError: (errors) => {
        // Você pode exibir erros de forma mais elaborada se necessário
        console.error("Erro ao excluir:", errors);
        alert("Ocorreu um erro ao excluir a categoria.");
      },
    });
  }
};
</script>

<style scoped>
/* Adicione estilos se necessário */
.btn-info,
.btn-danger,
.btn-new-category {
  color: #d4edda;
}
</style>
