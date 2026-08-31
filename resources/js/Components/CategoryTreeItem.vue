<template>
  <li>
    <Link :href="'/categories/show/' + category.slug">
      {{ category.name }}
    </Link>
    <ul v-if="category.children && category.children.length > 0">
      <CategoryTreeItem
        v-for="childCategory in category.children"
        :key="childCategory.id"
        :category="childCategory"
      />
    </ul>
  </li>
</template>

<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";

defineProps({
  category: {
    type: Object,
    required: true,
  },
});
</script>

<style scoped>
/* Estilos opcionais para a árvore */
ul {
  list-style: none;
  /* Remove marcadores da lista */
  padding-left: 20px;
  /* Indentação para subcategorias */
}

li {
  margin-bottom: 5px;
}

/* Adicione mais estilos conforme necessário */
</style>
