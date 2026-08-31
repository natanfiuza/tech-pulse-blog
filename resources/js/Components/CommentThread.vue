<template>
  <article class="flex gap-4">
    <img
      v-if="avatar_url"
      :src="avatar_url"
      :alt="comentario.user?.name || 'Avatar do usuário'"
      class="w-10 h-10 rounded-full object-cover flex-shrink-0 border border-slate-700"
    />
    <div
      v-else-if="comentario.user"
      class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold flex-shrink-0"
    >
      {{ inicial }}
    </div>

    <div class="flex-1 min-w-0">
      <div class="flex items-baseline gap-2 mb-1 flex-wrap">
        <span class="font-bold">{{ nome_autor }}</span>
        <span class="text-xs text-on-surface-variant">{{ data_relativa }}</span>
        <span v-if="eh_dono" class="text-[10px] uppercase tracking-wide text-primary">autor</span>
      </div>
      <p class="text-sm text-slate-300 leading-relaxed mb-2 break-words">
        {{ comentario.content }}
      </p>

      <div class="flex gap-4 text-xs text-on-surface-variant">
        <!-- Upvote (somente logado) -->
        <button
          v-if="usuario"
          type="button"
          :title="votado ? 'Remover upvote' : 'Dar upvote'"
          :aria-pressed="votado"
          :class="[
            'flex items-center gap-1 transition-colors',
            votado ? 'text-primary' : 'hover:text-primary',
          ]"
          :disabled="form_voto.processing"
          @click="alternar_voto"
        >
          <span
            class="material-symbols-outlined text-[16px]"
            :style="estilo_votado"
          >thumb_up</span>
          {{ votos_total }}
        </button>

        <!-- Responder (somente logado) -->
        <button
          v-if="usuario"
          type="button"
          class="hover:text-primary transition-colors"
          @click="alternar_resposta"
        >
          {{ respondendo ? "Cancelar" : "Responder" }}
        </button>

        <!-- Excluir (somente o dono) -->
        <button
          v-if="eh_dono"
          type="button"
          class="flex items-center gap-1 hover:text-error transition-colors"
          title="Excluir comentário"
          :disabled="form_excluir.processing"
          @click="excluir_comentario"
        >
          <span class="material-symbols-outlined text-[16px]">delete</span>
          Excluir
        </button>
      </div>

      <!-- Formulário de resposta -->
      <form v-if="respondendo" class="mt-4" @submit.prevent="enviar_resposta">
        <label :for="`resposta_${comentario.id}`" class="sr-only">Responder comentário</label>
        <textarea
          :id="`resposta_${comentario.id}`"
          v-model="conteudo_resposta"
          rows="2"
          maxlength="2000"
          required
          placeholder="Escreva sua resposta..."
          class="w-full bg-surface border border-outline-variant/50 rounded-lg px-3 py-2 text-sm text-white focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50 resize-y"
        ></textarea>
        <p v-if="form_resposta.errors.content" class="text-error text-sm mt-1">
          {{ form_resposta.errors.content }}
        </p>
        <button
          type="submit"
          :disabled="form_resposta.processing"
          class="mt-2 bg-primary text-white py-1.5 px-3 rounded-lg font-bold text-xs glow-hover transition-all disabled:opacity-50"
        >
          {{ form_resposta.processing ? "Enviando..." : "Responder" }}
        </button>
      </form>

      <div
        v-if="comentario.children && comentario.children.length"
        class="mt-6 space-y-6 border-l border-outline-variant/20 pl-5"
      >
        <CommentThread
          v-for="filho in comentario.children"
          :key="filho.id"
          :comentario="filho"
        />
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";

const props = defineProps({
  comentario: {
    type: Object,
    required: true,
  },
});

const usuario = computed(() => usePage().props.auth?.user ?? null);

const eh_dono = computed(
  () => usuario.value && props.comentario.user_id === usuario.value.id
);

const avatar_url = computed(() => props.comentario.user?.avatar || null);
// Usuário soft-deletado chega como null → "Usuário removido", sem foto
const nome_autor = computed(() => props.comentario.user?.name || "Usuário removido");
const inicial = computed(() => (nome_autor.value || "?").charAt(0).toUpperCase());
const data_relativa = computed(() => {
  if (!props.comentario.created_at) {
    return "";
  }
  return DateTime.fromISO(props.comentario.created_at).setLocale("pt-BR").toRelative();
});

const votado = computed(() => Boolean(props.comentario.has_upvoted));
const votos_total = computed(() => props.comentario.votes_count || 0);
const estilo_votado = computed(() =>
  votado.value ? "font-variation-settings: 'FILL' 1" : ""
);

const form_voto = useForm({});
const alternar_voto = () => {
  form_voto.post(route("comments.vote", { comment: props.comentario.id }), {
    preserveScroll: true,
  });
};

const form_excluir = useForm({});
const excluir_comentario = () => {
  if (confirm("Excluir este comentário?")) {
    form_excluir.delete(route("comments.destroy", { comment: props.comentario.id }), {
      preserveScroll: true,
    });
  }
};

const respondendo = ref(false);
const form_resposta = useForm({
  content: "",
  post_id: props.comentario.post_id,
  parent_id: props.comentario.id,
});
const conteudo_resposta = computed({
  get: () => form_resposta.content,
  set: (valor) => {
    form_resposta.content = valor;
  },
});

const alternar_resposta = () => {
  respondendo.value = !respondendo.value;
  if (!respondendo.value) {
    form_resposta.reset("content");
  }
};

const enviar_resposta = () => {
  form_resposta.post(route("comments.store"), {
    preserveScroll: true,
    onSuccess: () => {
      form_resposta.reset("content");
      respondendo.value = false;
    },
  });
};
</script>
