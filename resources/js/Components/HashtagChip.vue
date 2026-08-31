<template>
  <Link
    :href="`/tags/${hashtag.slug}`"
    class="no-underline bg-surface-container-low border border-outline-variant/20 hover:border-primary/50 px-2.5 py-1 rounded text-[10px] font-mono text-on-surface-variant hover:text-primary transition-all whitespace-nowrap"
  >
    #{{ hashtag.name }}
  </Link>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
  hashtag: {
    type: Object,
    required: true,
  },
});
</script>
