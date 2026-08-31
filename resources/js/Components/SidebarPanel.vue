<template>
  <section :class="classes_do_painel">
    <h4 v-if="titulo" :class="titulo_classes">
      <span v-if="icone" class="material-symbols-outlined text-lg">{{ icone }}</span>
      {{ titulo }}
    </h4>
    <slot />
  </section>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  titulo: {
    type: String,
    default: "",
  },
  icone: {
    type: String,
    default: "",
  },
  destaque: {
    type: Boolean,
    default: false,
  },
  sem_moldura: {
    type: Boolean,
    default: false,
  },
});

const classes_do_painel = computed(() => {
  if (props.sem_moldura) {
    return "space-y-6";
  }
  if (props.destaque) {
    return "bg-primary/10 rounded-xl p-6 md:p-8 border border-primary/30";
  }
  return "bg-surface-container-high rounded-xl p-6 md:p-8 border border-outline-variant/10";
});

const titulo_classes = computed(() => {
  if (props.sem_moldura) {
    return "text-xs font-black tracking-widest uppercase mb-6 flex items-center gap-2 text-on-surface";
  }
  return "text-xs font-black tracking-widest uppercase mb-6 text-primary";
});
</script>
