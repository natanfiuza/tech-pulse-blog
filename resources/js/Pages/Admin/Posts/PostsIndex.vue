<template>
  <AdminLayout>
    <h1>Posts</h1>
    <div v-if="successMessage" class="alert alert-success">
      {{ successMessage }}
    </div>
    <Link :href="'/admin/posts/create'" class="btn btn-primary mb-3 btn-new-post">
      Novo Post
    </Link>

    <table class="table table-sm">
      <thead>
        <tr>
          <th class="colspan">ID</th>
          <th>Título</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="post in posts" :key="post.id">
          <td>{{ post.id }}</td>
          <td>{{ post.title }}</td>
          <td>
            <!-- <Link :href="route('posts.edit', { uuid: post.uuid })">Editar</Link> -->
            <Link
              :href="'/admin/posts/edit/' + post.uuid"
              class="btn btn-primary btn-new-post"
              >Editar
            </Link>
            <Link
              :href="'/admin/posts/delete/' + post.uuid"
              method="delete"
              as="button"
              class="btn btn-danger btn-new-post"
              style="margin-left: 2px"
              >Excluir</Link
            >
          </td>
        </tr>
      </tbody>
    </table>
  </AdminLayout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";

export default {
  components: {
    Link,
    AdminLayout,
  },
  props: {
    posts: Array,
  },
  computed: {
    successMessage() {
      return this.$page.props.flash?.success; // Usa optional chaining aqui também
    },
  },
};
</script>

<style scoped>
/* Estilos para a mensagem (opcional - use as classes do seu framework CSS) */
.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 0.25rem;
}

.alert-success {
  background-color: #d4edda;
  border-color: #c3e6cb;
  color: #155724;
}
.btn-new-post {
  color: #d4edda;
}
/* Adicione estilos para .alert-error, .alert-info, etc., se precisar */
</style>
