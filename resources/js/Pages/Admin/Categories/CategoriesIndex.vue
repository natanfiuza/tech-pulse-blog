<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">
      <!-- Cabeçalho -->
      <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-on-surface md:text-3xl">Categorias</h1>
          <p class="mt-1 text-sm text-on-surface-variant">
            {{ categories_filtradas.length }} categoria(s) encontrada(s)
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
        v-if="categories_filtradas.length === 0"
        class="rounded-xl border border-dashed border-outline-variant/30 bg-surface-container-low p-12 text-center text-on-surface-variant"
      >
        <span v-if="termo_busca">Nenhuma categoria encontrada para "{{ termo_busca }}".</span>
        <span v-else>Nenhuma categoria encontrada.</span>
      </div>

      <!-- Lista de categorias -->
      <div class="space-y-4">
        <article
          v-for="category in categories_filtradas"
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
                class="inline-flex items-center gap-1.5 rounded-lg border border-error/30 bg-error/10 px-3 py-2 text-sm font-medium text-error transition-colors hover:bg-error/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error/60 cursor-pointer"
                @click="abrir_modal_exclusao(category)"
              >
                <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                Excluir
              </button>
            </div>
          </div>
        </article>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão de Categoria -->
    <ModalConfirmacao
      :aberto="modal_exclusao_aberto"
      titulo="Excluir Categoria"
      mensagem="Tem certeza que deseja excluir esta categoria permanentemente? Essa ação não pode ser desfeita."
      :item_nome="categoria_para_excluir?.name || ''"
      aviso_extra="Atenção: Os posts associados a esta categoria e eventuais subcategorias filhas terão suas vinculações desfeitas automaticamente."
      :processando="form_excluir.processing"
      rotulo_confirmar="Sim, excluir"
      rotulo_cancelar="Cancelar"
      @confirmar="confirmar_exclusao"
      @cancelar="cancelar_exclusao"
    />
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalConfirmacao from "@/Components/ModalConfirmacao.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { use_admin_busca } from "@/Composables/use_admin_busca";

export default {
    components: {
        AdminLayout,
        ModalConfirmacao,
        Link,
    },
    props: {
        categories: { type: Array, default: () => [] },
    },
    setup(props) {
        const form_excluir = useForm({});
        const modal_exclusao_aberto = ref(false);
        const categoria_para_excluir = ref(null);
        const { termo_busca, filtrar_categorias } = use_admin_busca();

        const categories_filtradas = computed(() => {
            return filtrar_categorias(props.categories);
        });

        function abrir_modal_exclusao(category) {
            categoria_para_excluir.value = category;
            modal_exclusao_aberto.value = true;
        }

        function cancelar_exclusao() {
            modal_exclusao_aberto.value = false;
            categoria_para_excluir.value = null;
        }

        function confirmar_exclusao() {
            if (!categoria_para_excluir.value) return;

            form_excluir.delete(route("categories.destroy", { category: categoria_para_excluir.value.id }), {
                preserveScroll: true,
                onSuccess: () => {
                    cancelar_exclusao();
                },
            });
        }

        return {
            modal_exclusao_aberto,
            categoria_para_excluir,
            form_excluir,
            abrir_modal_exclusao,
            cancelar_exclusao,
            confirmar_exclusao,
            termo_busca,
            categories_filtradas,
        };
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
