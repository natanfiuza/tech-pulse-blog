<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl space-y-6">
      <!-- Cabeçalho -->
      <div class="flex flex-wrap items-center justify-between gap-4">
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

      <!-- Cards de estatísticas (Clicáveis) -->
      <div
        class="grid grid-cols-1 gap-6 sm:grid-cols-2"
        :class="eh_admin ? 'xl:grid-cols-5' : 'xl:grid-cols-3'"
      >
        <Link
          v-for="stat in stats"
          :key="stat.label"
          :href="stat.href"
          class="group block rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl transition-all duration-300 hover:-translate-y-0.5 hover:border-primary/50 hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        >
          <div class="flex items-center justify-between">
            <span class="material-symbols-outlined text-3xl text-primary transition-transform group-hover:scale-110" aria-hidden="true">
              {{ stat.icon }}
            </span>
            <span class="rounded-full bg-secondary-container px-2 py-0.5 text-[10px] font-bold tracking-wide text-secondary uppercase">
              {{ stat.chip }}
            </span>
          </div>
          <p class="mt-4 font-mono text-3xl font-bold text-on-surface group-hover:text-primary transition-colors">
            {{ stat.value }}
          </p>
          <div class="mt-1 flex items-center justify-between">
            <p class="text-sm text-on-surface-variant">{{ stat.label }}</p>
            <span class="material-symbols-outlined text-xs text-on-surface-variant opacity-0 transition-opacity group-hover:opacity-100" aria-hidden="true">
              arrow_forward
            </span>
          </div>
        </Link>
      </div>

      <!-- Gráfico de Visualizações -->
      <VisualizacoesChart :dados="grafico_visualizacoes" />

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Status de publicação & Posts mais vistos -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl space-y-6"
          aria-labelledby="titulo-status"
        >
          <div>
            <h2 id="titulo-status" class="font-headline text-sm font-bold text-on-surface">Status de publicação</h2>
            <div class="mt-4 space-y-3">
              <Link
                v-for="status in statuses"
                :key="status.label"
                :href="status.href"
                class="flex items-center justify-between rounded-lg border border-outline-variant/20 bg-white dark:bg-surface-container-highest/40 px-4 py-3 shadow-sm dark:shadow-none transition-colors hover:border-primary/40 hover:bg-slate-50 dark:hover:bg-surface-container-high"
              >
                <div class="flex items-center gap-3">
                  <span class="h-2 w-2 rounded-full animate-pulse" :class="status.dot_class" aria-hidden="true"></span>
                  <span class="text-sm text-on-surface">{{ status.label }}</span>
                </div>
                <span class="font-mono text-sm text-on-surface-variant">{{ status.value }}</span>
              </Link>
            </div>
          </div>

          <!-- Posts mais vistos -->
          <div v-if="posts_mais_vistos && posts_mais_vistos.length > 0">
            <h3 class="font-headline text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3 flex items-center gap-1.5">
              <span class="material-symbols-outlined text-sm text-primary" aria-hidden="true">trending_up</span>
              Posts Mais Vistos
            </h3>
            <div class="space-y-2">
              <div
                v-for="post in posts_mais_vistos"
                :key="post.id"
                class="flex items-center justify-between gap-3 rounded-lg border border-outline-variant/15 bg-white dark:bg-surface-container-highest/30 px-3 py-2 text-xs shadow-sm dark:shadow-none"
              >
                <Link
                  :href="`/post/show/${post.slug}`"
                  class="font-medium text-on-surface hover:text-primary transition-colors truncate max-w-xs"
                  :title="post.title"
                >
                  {{ post.title }}
                </Link>
                <span class="font-mono text-primary font-bold shrink-0">
                  {{ post.views_count ?? 0 }} views
                </span>
              </div>
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
              class="flex items-center gap-3 rounded-lg border border-outline-variant/20 bg-white dark:bg-surface-container-highest/40 px-4 py-3 shadow-sm dark:shadow-none transition-colors hover:border-primary/40 hover:bg-slate-50 dark:hover:bg-surface-container-high focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
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
import VisualizacoesChart from "@/Components/Admin/VisualizacoesChart.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        AdminLayout,
        VisualizacoesChart,
        Link,
    },
    props: {
        metricas: {
            type: Object,
            default: () => ({}),
        },
        grafico_visualizacoes: {
            type: Array,
            default: () => [],
        },
        posts_mais_vistos: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        eh_admin() {
            return Boolean(this.metricas?.is_admin ?? this.$page.props.auth?.user?.role === "admin");
        },
        stats() {
            const itens = [
                {
                    label: "Posts publicados",
                    value: String(this.metricas?.posts_publicados ?? 0),
                    chip: "Ativo",
                    icon: "article",
                    href: "/admin/posts?status=publicado",
                },
                {
                    label: "Rascunhos",
                    value: String(this.metricas?.rascunhos ?? 0),
                    chip: "Em edição",
                    icon: "edit_note",
                    href: "/admin/posts?status=rascunho",
                },
                {
                    label: "Agendados",
                    value: String(this.metricas?.agendados ?? 0),
                    chip: "Futuro",
                    icon: "schedule",
                    href: "/admin/posts?status=agendado",
                },
            ];

            if (this.eh_admin) {
                itens.push({
                    label: "Categorias",
                    value: String(this.metricas?.categorias ?? 0),
                    chip: "Estrutura",
                    icon: "category",
                    href: "/admin/categories",
                });

                itens.push({
                    label: "Solicitações de Autor",
                    value: String(this.metricas?.solicitacoes_pendentes ?? 0),
                    chip: "Pendentes",
                    icon: "how_to_reg",
                    href: "/admin/solicitacoes-autor",
                });
            }

            return itens;
        },
        statuses() {
            return [
                {
                    label: "Publicado",
                    value: String(this.metricas?.posts_publicados ?? 0),
                    dot_class: "bg-emerald-400",
                    href: "/admin/posts?status=publicado",
                },
                {
                    label: "Agendado",
                    value: String(this.metricas?.agendados ?? 0),
                    dot_class: "bg-amber-400",
                    href: "/admin/posts?status=agendado",
                },
                {
                    label: "Rascunho",
                    value: String(this.metricas?.rascunhos ?? 0),
                    dot_class: "bg-secondary",
                    href: "/admin/posts?status=rascunho",
                },
            ];
        },
        acoes() {
            const lista = [
                { label: "Criar novo post", icon: "add_circle", href: "/admin/posts/create" },
                { label: "Gerenciar posts", icon: "article", href: "/admin/posts" },
            ];

            if (this.eh_admin) {
                lista.push({
                    label: "Gerenciar categorias",
                    icon: "category",
                    href: "/admin/categories",
                });
                lista.push({
                    label: "Solicitações de autor",
                    icon: "how_to_reg",
                    href: "/admin/solicitacoes-autor",
                });
            }

            return lista;
        },
    },
};
</script>
