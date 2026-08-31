<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">
      <!-- Cabeçalho -->
      <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-on-surface md:text-3xl">Posts</h1>
          <p class="mt-1 text-sm text-on-surface-variant">
            {{ posts.length }} post(s) encontrado(s)
          </p>
        </div>
        <Link
          href="/admin/posts/create"
          class="glow-hover inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-on-primary transition-all duration-300 hover:bg-surface-tint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        >
          <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
          Novo Post
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
        v-if="posts.length === 0"
        class="rounded-xl border border-dashed border-outline-variant/30 bg-surface-container-low p-12 text-center text-on-surface-variant"
      >
        Nenhum post encontrado.
      </div>

      <!-- Lista de posts -->
      <div class="space-y-4">
        <article
          v-for="post in posts"
          :key="post.uuid"
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl transition-colors hover:border-primary/30"
        >
          <div class="flex flex-col gap-4 md:flex-row md:items-start">
            <div class="min-w-0 flex-1">
              <div class="mb-2 flex flex-wrap items-center gap-2">
                <span
                  :class="status_chip_class(post.status)"
                  class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide uppercase"
                >
                  <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse" aria-hidden="true"></span>
                  {{ status_label(post.status) }}
                </span>
                <span
                  v-if="post.category"
                  class="rounded-full bg-primary-container px-2.5 py-0.5 text-[10px] font-bold text-primary"
                >
                  {{ post.category.name }}
                </span>
              </div>

              <h2 class="font-headline text-lg font-bold leading-snug text-on-surface">
                {{ post.title }}
              </h2>

              <div v-if="post.hashtags && post.hashtags.length" class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="tag in post.hashtags"
                  :key="tag.id"
                  class="rounded bg-secondary-container px-2 py-0.5 font-mono text-[10px] text-secondary"
                >
                  #{{ tag.name }}
                </span>
              </div>

              <p class="mt-3 font-mono text-xs text-on-surface-variant">
                Por <span class="text-on-surface">{{ nome_autor(post) }}</span> · Atualizado em
                {{ formatar_data(post.updated_at) }}
              </p>
            </div>

            <div class="flex shrink-0 items-center gap-2 md:flex-col md:items-end">
              <Link
                :href="route('posts.edit', { uuid: post.uuid })"
                class="inline-flex items-center gap-1.5 rounded-lg border border-outline-variant/20 bg-surface px-3 py-2 text-sm font-medium text-on-surface transition-colors hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
              >
                <span class="material-symbols-outlined text-base" aria-hidden="true">edit</span>
                Editar
              </Link>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-error/30 bg-error/10 px-3 py-2 text-sm font-medium text-error transition-colors hover:bg-error/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error/60"
                @click="excluir(post)"
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

const rotulos_status = {
    publicado: "Publicado",
    rascunho: "Rascunho",
    agendado: "Agendado",
};

const classes_status = {
    publicado: "bg-emerald-500/20 text-emerald-400",
    rascunho: "bg-surface-container-highest text-on-surface-variant",
    agendado: "bg-amber-500/20 text-amber-400",
};

export default {
    components: {
        AdminLayout,
        Link,
    },
    props: {
        posts: { type: Array, default: () => [] },
    },
    setup() {
        const form = useForm({});

        function excluir(post) {
            if (!window.confirm(`Excluir o post "${post.title}"? Essa ação não pode ser desfeita.`)) {
                return;
            }
            form.delete(route("posts.destroy", { uuid: post.uuid }));
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
    methods: {
        nome_autor(post) {
            return post.user ? post.user.name : "Usuário removido";
        },
        status_label(status) {
            return rotulos_status[status] ?? status;
        },
        status_chip_class(status) {
            return classes_status[status] ?? classes_status.rascunho;
        },
        formatar_data(valor) {
            if (!valor) {
                return "-";
            }
            const data = new Date(valor);
            if (Number.isNaN(data.getTime())) {
                return "-";
            }
            return data.toLocaleDateString("pt-BR");
        },
    },
};
</script>
