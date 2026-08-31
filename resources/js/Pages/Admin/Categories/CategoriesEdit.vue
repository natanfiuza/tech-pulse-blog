<template>
  <AdminLayout>
    <div class="mx-auto max-w-3xl">
      <h1 class="mb-6 font-headline text-2xl font-extrabold text-on-surface md:text-3xl">
        Editar Categoria: {{ form.name }}
      </h1>

      <form
        @submit.prevent="submit"
        class="space-y-5 rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
      >
        <!-- Nome -->
        <div>
          <label for="name" class="mb-2 block font-headline text-sm font-bold text-on-surface">Nome</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-error" role="alert">{{ form.errors.name }}</p>
        </div>

        <!-- Categoria Pai -->
        <div>
          <label for="parent_id" class="mb-2 block font-headline text-sm font-bold text-on-surface">
            Categoria Pai
          </label>
          <select
            id="parent_id"
            v-model="form.parent_id"
            class="w-full appearance-none rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          >
            <option :value="null">-- Nenhuma --</option>
            <option v-for="parent in categories" :key="parent.id" :value="parent.id">
              {{ parent.name }}
            </option>
          </select>
          <p v-if="form.errors.parent_id" class="mt-1 text-sm text-error" role="alert">
            {{ form.errors.parent_id }}
          </p>
        </div>

        <!-- Descrição -->
        <div>
          <label for="description" class="mb-2 block font-headline text-sm font-bold text-on-surface">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
          <p v-if="form.errors.description" class="mt-1 text-sm text-error" role="alert">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Abrangência -->
        <div>
          <label for="scope" class="mb-2 block font-headline text-sm font-bold text-on-surface">Abrangência</label>
          <textarea
            id="scope"
            v-model="form.scope"
            rows="3"
            class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
          <p v-if="form.errors.scope" class="mt-1 text-sm text-error" role="alert">{{ form.errors.scope }}</p>
        </div>

        <!-- Possíveis Conteúdos -->
        <div>
          <label for="possible_contents" class="mb-2 block font-headline text-sm font-bold text-on-surface">
            Possíveis Conteúdos
          </label>
          <textarea
            id="possible_contents"
            v-model="form.possible_contents"
            rows="5"
            class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
          <p v-if="form.errors.possible_contents" class="mt-1 text-sm text-error" role="alert">
            {{ form.errors.possible_contents }}
          </p>
        </div>

        <!-- Sugestões de Postagens -->
        <div>
          <label for="post_suggestions" class="mb-2 block font-headline text-sm font-bold text-on-surface">
            Sugestões de Postagens
          </label>
          <textarea
            id="post_suggestions"
            v-model="form.post_suggestions"
            rows="5"
            class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-highest px-3 py-2.5 text-sm text-on-surface transition-colors placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
          <p v-if="form.errors.post_suggestions" class="mt-1 text-sm text-error" role="alert">
            {{ form.errors.post_suggestions }}
          </p>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
          <button
            type="submit"
            class="glow-hover rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-on-primary transition-all duration-300 hover:bg-surface-tint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Atualizar Categoria
          </button>
          <Link
            href="/admin/categories"
            class="rounded-lg border border-outline-variant/20 bg-surface px-5 py-2.5 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
          >
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

// A categoria sendo editada e os pais disponíveis
const props = defineProps({
    category: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.category.name,
    description: props.category.description ?? "",
    scope: props.category.scope ?? "",
    possible_contents: props.category.possible_contents ?? "",
    post_suggestions: props.category.post_suggestions ?? "",
    parent_id: props.category.parent_id ?? null,
});

const submit = () => {
    form.put(route("categories.update", { category: props.category.id }));
};
</script>
