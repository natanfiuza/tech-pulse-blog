<template>
  <AdminLayout>
    <h1>Categorias</h1>
    <div v-if="successMessage" class="alert alert-success">
      {{ successMessage }}
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
                :href="'/admin/categories/edit/' + category.uuid"
                class="btn btn-sm btn-primary me-2 btn-edit-category"
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
<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Link,
    AdminLayout,
  },
  props: {
    categories: Array,
  },
  computed: {
    successMessage() {
      return this.$page.props.flash?.success; // Usa optional chaining aqui também
    },
  },
};
</script>
<style scoped>
/* Adicione estilos se necessário */
.btn-info,
.btn-danger,
.btn-new-category {
  color: #d4edda;
}

.btn-edit-category {
  color: #d4edda;
}
</style>
