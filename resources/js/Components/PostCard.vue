<template>
  <article
    class="bg-surface-container-low rounded-xl overflow-hidden border border-outline-variant/20 group hover:border-primary/40 transition-all duration-300 flex flex-col"
  >
    <Link :href="post_url" class="block aspect-video overflow-hidden no-underline">
      <img
        v-if="imagem_url"
        :src="imagem_url"
        :alt="post.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 block"
        loading="lazy"
      />
      <div
        v-else
        class="w-full h-full bg-gradient-to-br from-primary/30 to-surface-container-high flex items-center justify-center"
      >
        <span class="material-symbols-outlined text-5xl text-primary/60">code</span>
      </div>
    </Link>

    <div class="p-6 flex flex-col flex-1">
      <div class="flex justify-between items-start gap-2 mb-4">
        <CategoryChip v-if="post.category" :category="post.category" />
        <span class="flex items-center gap-2 ml-auto pt-0.5">
          <span class="text-on-surface-variant text-[10px] font-mono uppercase">
            Por {{ nome_autor }}
          </span>
          <span class="text-on-surface-variant text-[10px] font-mono uppercase">
            {{ data_formatada }}
          </span>
        </span>
      </div>

      <Link :href="post_url" class="no-underline">
        <h3
          class="text-xl font-bold mb-3 group-hover:text-primary transition-colors leading-snug line-clamp-2"
        >
          {{ post.title }}
        </h3>
      </Link>

      <p class="text-on-surface-variant text-sm mb-6 line-clamp-2 leading-relaxed">
        {{ post.excerpt }}
      </p>

      <div v-if="post.hashtags && post.hashtags.length" class="flex flex-wrap gap-2 mb-4">
        <HashtagChip
          v-for="hashtag in post.hashtags.slice(0, 3)"
          :key="hashtag.id"
          :hashtag="hashtag"
        />
      </div>

      <div
        class="flex items-center justify-between mt-auto pt-4 border-t border-outline-variant/10"
      >
        <span class="text-[11px] font-bold text-on-surface-variant flex items-center gap-1">
          <span class="material-symbols-outlined text-sm">schedule</span>
          {{ tempo_leitura_min }} min de leitura
        </span>
        <Link
          :href="post_url"
          class="text-primary text-xs font-black uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all no-underline"
        >
          Ler <span class="material-symbols-outlined text-sm">chevron_right</span>
        </Link>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import { tempo_leitura, url_da_imagem } from "@/helpers";
import CategoryChip from "@/Components/CategoryChip.vue";
import HashtagChip from "@/Components/HashtagChip.vue";

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const post_url = computed(() => `/post/show/${props.post.slug}`);
const imagem_url = computed(() => url_da_imagem(props.post));
// Autor removido (soft delete) chega como null → "Usuário removido"
const nome_autor = computed(() => props.post.user?.name || "Usuário removido");
const tempo_leitura_min = computed(() => {
  const texto = props.post.content || props.post.excerpt || "";
  return tempo_leitura(texto);
});
const data_formatada = computed(() => {
  if (!props.post.created_at) {
    return "";
  }
  return DateTime.fromISO(props.post.created_at)
    .setLocale("pt-BR")
    .toFormat("dd LLL, yyyy");
});
</script>
