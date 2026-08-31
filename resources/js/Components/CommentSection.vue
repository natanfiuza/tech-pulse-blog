<template>
  <section class="bg-surface-container-high rounded-2xl p-6 md:p-8 border border-outline-variant/20">
    <h3 class="text-xl md:text-2xl font-bold mb-8">Discussão ({{ total_comentarios }})</h3>

    <!-- Formulário (somente usuários logados) -->
    <form v-if="usuario" class="mb-8" @submit.prevent="enviar_comentario">
      <label for="novo_comentario" class="sr-only">Seu comentário</label>
      <textarea
        id="novo_comentario"
        v-model="novo_comentario"
        rows="3"
        maxlength="2000"
        required
        placeholder="Escreva um comentário..."
        class="w-full bg-surface border border-outline-variant/50 rounded-lg px-4 py-3 text-sm text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50 resize-y"
      ></textarea>
      <div class="flex items-center justify-between gap-4 mt-2">
        <p v-if="form.errors.content" class="text-error text-sm">{{ form.errors.content }}</p>
        <p v-else class="text-xs text-on-surface-variant truncate">
          Comentando como <span class="font-medium text-white">{{ usuario.name }}</span>
        </p>
        <button
          type="submit"
          :disabled="form.processing"
          class="bg-primary text-white py-2 px-4 rounded-lg font-bold text-sm glow-hover transition-all shrink-0 disabled:opacity-50"
        >
          {{ form.processing ? "Enviando..." : "Comentar" }}
        </button>
      </div>
    </form>

    <!-- Chamada para login -->
    <div v-else class="mb-8 p-4 rounded-lg bg-surface/50 border border-outline-variant/20">
      <p class="text-sm text-on-surface-variant mb-3">
        Participe da discussão! Entre com sua conta para comentar e votar.
      </p>
      <Link
        :href="route('login')"
        class="inline-flex items-center gap-2 bg-primary text-white py-2 px-4 rounded-lg font-bold text-sm glow-hover transition-all"
      >
        <span class="material-symbols-outlined text-[16px]">login</span>
        Entrar para comentar
      </Link>
    </div>

    <p v-if="total_comentarios === 0" class="text-on-surface-variant text-sm">
      Nenhum comentário ainda. Seja o primeiro a contribuir com a discussão.
    </p>

    <div v-else class="space-y-8">
      <CommentThread
        v-for="comentario in comentarios_raiz"
        :key="comentario.id"
        :comentario="comentario"
      />
    </div>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import CommentThread from "@/Components/CommentThread.vue";

const props = defineProps({
  comments: {
    type: Array,
    default: () => [],
  },
  post_id: {
    type: Number,
    required: true,
  },
});

const usuario = computed(() => usePage().props.auth?.user ?? null);

const form = useForm({
  content: "",
  post_id: props.post_id,
});

const novo_comentario = computed({
  get: () => form.content,
  set: (valor) => {
    form.content = valor;
  },
});

const enviar_comentario = () => {
  form.post(route("comments.store"), {
    preserveScroll: true,
    onSuccess: () => form.reset("content"),
  });
};

const comentarios_raiz = computed(() =>
  props.comments.filter((comentario) => !comentario.parent_id)
);

const contar_comentarios = (comentarios) => {
  let total = 0;
  comentarios.forEach((comentario) => {
    total += 1;
    if (comentario.children && comentario.children.length) {
      total += contar_comentarios(comentario.children);
    }
  });
  return total;
};

const total_comentarios = computed(() => contar_comentarios(props.comments));
</script>
