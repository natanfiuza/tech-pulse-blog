<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">
      <!-- Cabeçalho -->
      <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-on-surface md:text-3xl">Dashboard</h1>
          <p class="mt-1 text-sm text-on-surface-variant">Visão geral do TechPulse Admin</p>
        </div>
        <Link
          href="/admin/posts/create"
          class="glow-hover inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-on-primary transition-all duration-300 hover:bg-surface-tint focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        >
          <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
          Novo Post
        </Link>
      </div>

      <!-- Cards de estatísticas -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div
          v-for="stat in stats"
          :key="stat.label"
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between">
            <span class="material-symbols-outlined text-3xl text-primary" aria-hidden="true">{{ stat.icon }}</span>
            <span class="rounded-full bg-secondary-container px-2 py-0.5 text-[10px] font-bold tracking-wide text-secondary uppercase">
              {{ stat.chip }}
            </span>
          </div>
          <p class="mt-4 font-mono text-3xl font-bold text-on-surface">{{ stat.value }}</p>
          <p class="mt-1 text-sm text-on-surface-variant">{{ stat.label }}</p>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Status de publicação -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
          aria-labelledby="titulo-status"
        >
          <h2 id="titulo-status" class="font-headline text-sm font-bold text-on-surface">Status de publicação</h2>
          <div class="mt-4 space-y-3">
            <div
              v-for="status in statuses"
              :key="status.label"
              class="flex items-center justify-between rounded-lg border border-outline-variant/20 bg-surface-container-highest/40 px-4 py-3"
            >
              <div class="flex items-center gap-3">
                <span class="h-2 w-2 rounded-full animate-pulse" :class="status.dot_class" aria-hidden="true"></span>
                <span class="text-sm text-on-surface">{{ status.label }}</span>
              </div>
              <span class="font-mono text-sm text-on-surface-variant">{{ status.value }}</span>
            </div>
          </div>
        </section>

        <!-- Ações rápidas -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
          aria-labelledby="titulo-acoes"
        >
          <h2 id="titulo-acoes" class="font-headline text-sm font-bold text-on-surface">Ações rápidas</h2>
          <div class="mt-4 space-y-3">
            <Link
              v-for="acao in acoes"
              :key="acao.href"
              :href="acao.href"
              class="flex items-center gap-3 rounded-lg border border-outline-variant/20 bg-surface-container-highest/40 px-4 py-3 transition-colors hover:border-primary/40 hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
            >
              <span class="material-symbols-outlined text-primary" aria-hidden="true">{{ acao.icon }}</span>
              <span class="text-sm font-medium text-on-surface">{{ acao.label }}</span>
              <span class="material-symbols-outlined ml-auto text-sm text-on-surface-variant" aria-hidden="true">chevron_right</span>
            </Link>
          </div>
        </section>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        AdminLayout,
        Link,
    },
    data() {
        return {
            stats: [
                { label: "Posts publicados", value: "12", chip: "Ativo", icon: "article" },
                { label: "Rascunhos", value: "3", chip: "Em edição", icon: "edit_note" },
                { label: "Agendados", value: "1", chip: "Futuro", icon: "schedule" },
                { label: "Categorias", value: "8", chip: "Estrutura", icon: "category" },
            ],
            statuses: [
                { label: "Publicado", value: "12", dot_class: "bg-emerald-400" },
                { label: "Agendado", value: "1", dot_class: "bg-amber-400" },
                { label: "Rascunho", value: "3", dot_class: "bg-secondary" },
            ],
            acoes: [
                { label: "Criar novo post", icon: "add_circle", href: "/admin/posts/create" },
                { label: "Gerenciar posts", icon: "article", href: "/admin/posts" },
                { label: "Gerenciar categorias", icon: "category", href: "/admin/categories" },
            ],
        };
    },
};
</script>
