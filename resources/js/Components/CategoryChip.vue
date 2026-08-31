<template>
  <span
    class="bg-primary/20 text-primary text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wider whitespace-nowrap"
  >
    {{ category.name }}
  </span>
</template>

<script setup>
defineProps({
  category: {
    type: Object,
    required: true,
  },
});
</script>
