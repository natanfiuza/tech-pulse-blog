<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">
      <h1 class="mb-6 font-headline text-2xl font-extrabold text-on-surface md:text-3xl">Novo Post</h1>

      <form @submit.prevent="submit('publicado')" class="flex flex-col items-start gap-8 lg:flex-row">
        <!-- Coluna principal -->
        <div class="w-full flex-1 space-y-6">
          <!-- Título -->
          <div
            class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl transition-colors focus-within:border-primary/50"
          >
            <label for="post_title" class="sr-only">Título do post</label>
            <input
              id="post_title"
              v-model="form.title"
              type="text"
              autofocus
              class="w-full border-none bg-transparent p-0 font-headline text-2xl font-extrabold text-on-surface placeholder:text-on-surface-variant/40 focus:outline-none md:text-3xl"
              placeholder="Título do Post..."
            />
          </div>
          <p v-if="form.errors.title" class="-mt-4 text-sm text-error" role="alert">{{ form.errors.title }}</p>

          <!-- Editor Markdown (EasyMDE) -->
          <div
            class="overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-high shadow-2xl"
          >
            <MarkdownEditor v-model="original_content" />
          </div>
          <p v-if="form.errors.content" class="text-sm text-error" role="alert">{{ form.errors.content }}</p>

          <!-- Resumo -->
          <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl">
            <label for="post_excerpt" class="mb-2 block font-headline text-sm font-bold text-on-surface">
              Resumo
            </label>
            <textarea
              id="post_excerpt"
              v-model="form.excerpt"
              rows="4"
              class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
              placeholder="Breve resumo exibido nas listagens do blog..."
            ></textarea>
            <p v-if="form.errors.excerpt" class="mt-1 text-sm text-error" role="alert">{{ form.errors.excerpt }}</p>
          </div>
        </div>

        <!-- Coluna de configurações -->
        <div class="w-full shrink-0 space-y-6 lg:w-80">
          <!-- Ações -->
          <div class="flex flex-col gap-3 rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl">
            <button
              type="submit"
              class="glow-hover w-full rounded-lg bg-primary py-3 px-4 font-medium text-on-primary transition-all duration-300 hover:bg-surface-tint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing"
            >
              Publicar
            </button>
            <button
              type="button"
              class="w-full rounded-lg border border-outline-variant/20 bg-surface py-3 px-4 font-medium text-on-surface transition-all duration-300 hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing"
              @click="submit('rascunho')"
            >
              Salvar Rascunho
            </button>
          </div>

          <!-- Imagem de destaque -->
          <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl">
            <h3 class="mb-4 font-headline text-sm font-bold text-on-surface">Imagem de Destaque</h3>
            <ImageDropzone v-model:model_value="form.image" :error="form.errors.image" />
          </div>

          <!-- Metadados -->
          <div class="space-y-5 rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl">
            <!-- Categoria -->
            <div>
              <label for="post_category" class="mb-2 block font-headline text-sm font-bold text-on-surface">
                Categoria
              </label>
              <select
                id="post_category"
                v-model="form.category_id"
                class="w-full appearance-none rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none"
              >
                <option :value="null">Selecione uma categoria...</option>
                <option
                  v-for="categoria in categorias_planas"
                  :key="categoria.id"
                  :value="categoria.id"
                >
                  {{ "  ".repeat(categoria.nivel) }}{{ categoria.name }}
                </option>
              </select>
              <p v-if="form.errors.category_id" class="mt-1 text-sm text-error" role="alert">
                {{ form.errors.category_id }}
              </p>
            </div>

            <!-- Tags -->
            <div>
              <label for="post_tags" class="mb-2 block font-headline text-sm font-bold text-on-surface">Tags</label>
              <TagInput v-model:model_value="form.hashtags" :sugestoes="hashtags_existentes" />
              <p v-if="form.errors.hashtags" class="mt-1 text-sm text-error" role="alert">
                {{ form.errors.hashtags }}
              </p>
            </div>

            <!-- Agendamento -->
            <div>
              <label for="post_published_at" class="mb-2 block font-headline text-sm font-bold text-on-surface">
                Agendamento
              </label>
              <div class="relative">
                <span
                  class="material-symbols-outlined absolute top-1/2 left-3 -translate-y-1/2 text-sm text-on-surface-variant"
                  aria-hidden="true"
                >calendar_today</span>
                <input
                  id="post_published_at"
                  v-model="form.published_at"
                  type="datetime-local"
                  title="Data e hora de publicação"
                  class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest py-2.5 pr-3 pl-9 text-sm text-on-surface transition-colors focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none"
                />
              </div>
              <p v-if="form.errors.published_at" class="mt-1 text-sm text-error" role="alert">
                {{ form.errors.published_at }}
              </p>
              <p class="mt-2 text-xs text-on-surface-variant">Deixe em branco para publicar imediatamente.</p>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MarkdownEditor from "@/Components/Admin/MarkdownEditor.vue";
import ImageDropzone from "@/Components/Admin/ImageDropzone.vue";
import TagInput from "@/Components/Admin/TagInput.vue";

const props = defineProps({
    categorias: { type: Array, default: () => [] },
    hashtags_existentes: { type: Array, default: () => [] },
});

// Conteúdo real do editor (markdown, antes da codificação base64)
const original_content = ref("");

const form = useForm({
    title: "",
    excerpt: "",
    category_id: null,
    status: "publicado",
    published_at: "",
    hashtags: [],
    image: null,
});

// Achata a árvore de categorias (raízes com children.children) para o select
const categorias_planas = computed(() => {
    const resultado = [];
    for (const raiz of props.categorias) {
        resultado.push({ id: raiz.id, name: raiz.name, nivel: 0 });
        for (const filho of raiz.children ?? []) {
            resultado.push({ id: filho.id, name: filho.name, nivel: 1 });
            for (const neto of filho.children ?? []) {
                resultado.push({ id: neto.id, name: neto.name, nivel: 2 });
            }
        }
    }
    return resultado;
});

function codificar_conteudo() {
    try {
        const utf8_bytes = new TextEncoder().encode(original_content.value);
        const binary_string = String.fromCharCode(...utf8_bytes);
        return btoa(binary_string);
    } catch (e) {
        form.setError("content", "Não foi possível codificar o conteúdo para envio.");
        return null;
    }
}

function submit(status) {
    const encoded_content = codificar_conteudo();
    if (encoded_content === null) {
        return;
    }

    form
        .transform((data) => ({
            ...data,
            content: encoded_content,
            status,
        }))
        .post(route("posts.store"), {
            forceFormData: form.image instanceof File,
        });
}
</script>
