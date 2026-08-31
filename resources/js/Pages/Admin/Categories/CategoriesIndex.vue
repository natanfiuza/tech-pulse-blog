<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">
      <!-- Cabeçalho -->
      <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-on-surface md:text-3xl">Categorias</h1>
          <p class="mt-1 text-sm text-on-surface-variant">
            {{ categories.length }} categoria(s) encontrada(s)
          </p>
        </div>
        <Link
          href="/admin/categories/create"
          class="glow-hover inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-on-primary transition-all duration-300 hover:bg-surface-tint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        >
          <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
          Nova Categoria
        </Link>
      </div>

      <!-- Mensagens flash -->
      <div
        v-if="success_message"
        class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
        role="status"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">check_circle</span>
        {{ success_message }}
      </div>
      <div
        v-if="error_message"
        class="mb-6 flex items-center gap-2 rounded-lg border border-error/30 bg-error/10 px-4 py-3 text-sm text-error"
        role="alert"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">error</span>
        {{ error_message }}
      </div>

      <!-- Lista vazia -->
      <div
        v-if="categories.length === 0"
        class="rounded-xl border border-dashed border-outline-variant/30 bg-surface-container-low p-12 text-center text-on-surface-variant"
      >
        Nenhuma categoria encontrada.
      </div>

      <!-- Lista de categorias -->
      <div class="space-y-4">
        <article
          v-for="category in categories"
          :key="category.id"
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl transition-colors hover:border-primary/30"
        >
          <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-headline text-lg font-bold text-on-surface">{{ category.name }}</h2>
                <span
                  v-if="category.parent"
                  class="inline-flex items-center gap-1 rounded-full bg-secondary-container px-2.5 py-0.5 text-[10px] font-bold text-secondary"
                >
                  <span class="material-symbols-outlined text-[12px]" aria-hidden="true">subdirectory_arrow_right</span>
                  {{ category.parent.name }}
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 rounded-full bg-primary-container px-2.5 py-0.5 text-[10px] font-bold text-primary"
                >
                  <span class="material-symbols-outlined text-[12px]" aria-hidden="true">account_tree</span>
                  Raiz
                </span>
              </div>
              <p class="mt-1 font-mono text-xs text-on-surface-variant">/{{ category.slug }}</p>
              <p v-if="category.description" class="mt-2 text-sm text-on-surface-variant/80">
                {{ category.description }}
              </p>
            </div>

            <div class="flex shrink-0 items-center gap-2 md:flex-col md:items-end">
              <Link
                :href="route('categories.edit', { category: category.id })"
                class="inline-flex items-center gap-1.5 rounded-lg border border-outline-variant/20 bg-surface px-3 py-2 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
              >
                <span class="material-symbols-outlined text-base" aria-hidden="true">edit</span>
                Editar
              </Link>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-error/30 bg-error/10 px-3 py-2 text-sm font-medium text-error transition-colors hover:bg-error/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error/60"
                @click="excluir(category)"
              >
                <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                Excluir
              </button>
            </div>
          </div>
        </article>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
    components: {
        AdminLayout,
        Link,
    },
    props: {
        categories: { type: Array, default: () => [] },
    },
    setup() {
        const form = useForm({});

        function excluir(category) {
            if (!window.confirm(`Excluir a categoria "${category.name}"? Essa ação não pode ser desfeita.`)) {
                return;
            }
            form.delete(route("categories.destroy", { category: category.id }));
        }

        return { excluir };
    },
    computed: {
        success_message() {
            return this.$page.props.flash?.success;
        },
        error_message() {
            return this.$page.props.flash?.error;
        },
    },
};
</script>
