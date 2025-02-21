<template>
  <!-- <span>
        <InertiaLink :href="'/categories/show/' + category.slug">
            {{ category.name }}
        </InertiaLink>
        <span v-if="category.children && category.children.length">
            <span v-for="child in category.children" :key="child.id">
                <span v-if="child.children && child.children.length">
                    /
                    <CategoryName :category="child" />
                </span>
                <span v-else>
                    /
                    <InertiaLink :href="'/categories/show/' + child.slug">
                        {{ child.name }}
                    </InertiaLink>
                </span>
            </span>
        </span>
    </span> -->
  <li>
    <InertiaLink :href="'/categories/show/' + category.slug">
      {{ category.name }}
    </InertiaLink>
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
import { InertiaLink } from "@inertiajs/inertia-vue3";
defineProps({
  category: Object,
});
</script>
<style scoped>
ul {
  list-style: none;
  /* Remove marcadores da lista */
  padding-left: 0px;
  /* Zera o padding inicial */
  margin-top: 0.2rem;
}

li {
  margin-bottom: 5px;
  padding-left: 0px;
  /*  Indentação inicial */
}

li li {
  margin-left: 1rem;
  /*Adiciona a identação*/
}
</style>
