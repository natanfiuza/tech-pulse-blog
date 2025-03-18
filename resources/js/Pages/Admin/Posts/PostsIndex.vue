<template>
  <AdminLayout>
    <h1>Posts</h1>
    <Link :href="'/admin/posts/create'" class="button">Novo Post</Link>
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
            <Link :href="'/posts/edit/' + post.uuid">Editar</Link>
            <Link
              :href="'/posts/edit/' + post.uuid"
              method="delete"
              as="button"
              type="button"
              >Excluir
            </Link>
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
};
</script>
