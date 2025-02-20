<template>
  <span>
    <InertiaLink :href="route('categories.show', { slug: category.slug })">
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
          <InertiaLink :href="route('categories.show', { slug: child.slug })">
            {{ child.name }}
          </InertiaLink>
        </span>
      </span>
    </span>
  </span>
</template>

<script setup>
import { defineProps } from "vue";
import { InertiaLink } from "@inertiajs/inertia-vue3";
defineProps({
  category: Object,
});
</script>
