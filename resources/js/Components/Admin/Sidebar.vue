<template>
  <aside class="sidebar">
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
};
</script>

<style scoped>
.sidebar {
  margin-left: -7px;
  background-color: rgb(142, 196, 233);
  margin-right: 5px;
}
.sidebar ul {
  padding-right: 10px;
}
</style>
