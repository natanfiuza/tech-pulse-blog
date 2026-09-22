<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">

      <!-- Cabeçalho -->
      <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-on-surface md:text-3xl">
            Solicitações de Autor
          </h1>
          <p class="mt-1 text-sm text-on-surface-variant">
            {{ solicitacoes.length }} solicitação(ões) ·
            {{ pendentes.length }} pendente(s)
          </p>
        </div>
      </div>

      <!-- Flash -->
      <div
        v-if="flash_success"
        class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
        role="status"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">check_circle</span>
        {{ flash_success }}
      </div>
      <div
        v-if="flash_error"
        class="mb-6 flex items-center gap-2 rounded-lg border border-error/30 bg-error/10 px-4 py-3 text-sm text-error"
        role="alert"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">error</span>
        {{ flash_error }}
      </div>

      <!-- Filtro de status -->
      <div class="mb-6 flex flex-wrap gap-2">
        <button
          v-for="f in filtros"
          :key="f.valor"
          type="button"
          :class="filtro_ativo === f.valor
            ? 'bg-primary text-on-primary'
            : 'bg-surface-container-high text-on-surface-variant hover:text-on-surface border border-outline-variant/20'"
          class="rounded-full px-4 py-1.5 text-xs font-bold tracking-wider uppercase transition-colors"
          @click="filtro_ativo = f.valor"
        >
          {{ f.rotulo }}
          <span class="ml-1 font-mono">
            ({{ f.valor === 'todos' ? solicitacoes.length : solicitacoes.filter(s => s.status === f.valor).length }})
          </span>
        </button>
      </div>

      <!-- Lista vazia -->
      <div
        v-if="solicitacoes_filtradas.length === 0"
        class="rounded-xl border border-dashed border-outline-variant/30 bg-surface-container-low p-12 text-center text-on-surface-variant"
      >
        Nenhuma solicitação encontrada.
      </div>

      <!-- Cards de solicitação -->
      <div class="space-y-4">
        <article
          v-for="sol in solicitacoes_filtradas"
          :key="sol.id"
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl transition-colors hover:border-primary/20"
        >
          <!-- Cabeçalho do card -->
          <div class="flex flex-col gap-4 md:flex-row md:items-start">
            <!-- Avatar + info do usuário -->
            <div class="flex min-w-0 flex-1 items-start gap-4">
              <img
                v-if="sol.user?.avatar_url"
                :src="sol.user.avatar_url"
                :alt="`Foto de ${sol.user?.name}`"
                class="h-12 w-12 rounded-full object-cover border border-outline-variant/30 shrink-0"
              />
              <div
                v-else
                class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/20 font-bold text-primary shrink-0"
              >
                {{ inicial(sol.user) }}
              </div>
              <div class="min-w-0">
                <h2 class="font-headline text-base font-bold text-on-surface">
                  {{ sol.user?.name ?? 'Usuário removido' }}
                </h2>
                <p class="text-sm text-on-surface-variant">{{ sol.user?.email }}</p>
                <p class="mt-1 font-mono text-xs text-on-surface-variant">
                  Solicitado em {{ sol.created_at }}
                </p>
              </div>
            </div>

            <!-- Chip de status -->
            <div class="flex shrink-0 items-center gap-2">
              <span
                :class="chip_status(sol.status)"
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-wide uppercase"
              >
                {{ rotulo_status(sol.status) }}
              </span>
            </div>
          </div>

          <!-- Motivação -->
          <div class="mt-4 rounded-lg bg-surface-container-high/60 p-4">
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Motivação</p>
            <p class="text-sm text-on-surface leading-relaxed whitespace-pre-line">{{ sol.motivacao }}</p>
          </div>

          <!-- Nota do admin (se houver) -->
          <div v-if="sol.admin_nota" class="mt-3 rounded-lg bg-surface-container-highest/60 p-3">
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">
              Nota ({{ sol.admin?.name ?? 'Admin' }})
            </p>
            <p class="text-sm text-on-surface-variant">{{ sol.admin_nota }}</p>
          </div>

          <!-- Ações (somente para pendentes) -->
          <div v-if="sol.status === 'pendente'" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
              <label :for="`nota_${sol.id}`" class="block text-xs font-bold text-on-surface-variant mb-1">
                Nota interna (opcional)
              </label>
              <input
                :id="`nota_${sol.id}`"
                v-model="notas[sol.id]"
                type="text"
                placeholder="Escreva uma nota para o solicitante ou para o histórico..."
                class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
              />
            </div>
            <div class="flex gap-2 shrink-0">
              <button
                type="button"
                :disabled="procesando[sol.id]"
                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 px-4 py-2 text-sm font-medium text-white transition-colors disabled:opacity-50 cursor-pointer"
                @click="revisar(sol.id, 'aprovar')"
              >
                <span class="material-symbols-outlined text-base" aria-hidden="true">how_to_reg</span>
                Aprovar
              </button>
              <button
                type="button"
                :disabled="procesando[sol.id]"
                class="inline-flex items-center gap-1.5 rounded-lg border border-error/30 bg-error/10 px-4 py-2 text-sm font-medium text-error hover:bg-error/20 transition-colors disabled:opacity-50 cursor-pointer"
                @click="revisar(sol.id, 'rejeitar')"
              >
                <span class="material-symbols-outlined text-base" aria-hidden="true">cancel</span>
                Rejeitar
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
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const status_chips = {
  pendente:  "bg-amber-500/20 text-amber-500",
  aprovada:  "bg-emerald-500/20 text-emerald-500",
  rejeitada: "bg-error/20 text-error",
};

const status_rotulos = {
  pendente:  "Pendente",
  aprovada:  "Aprovada",
  rejeitada: "Rejeitada",
};

export default {
  components: { AdminLayout },

  props: {
    solicitacoes: { type: Array, default: () => [] },
  },

  setup(props) {
    const page = usePage();

    const filtro_ativo = ref("todos");

    const filtros = [
      { valor: "todos",     rotulo: "Todas" },
      { valor: "pendente",  rotulo: "Pendentes" },
      { valor: "aprovada",  rotulo: "Aprovadas" },
      { valor: "rejeitada", rotulo: "Rejeitadas" },
    ];

    const pendentes = computed(() =>
      props.solicitacoes.filter((s) => s.status === "pendente")
    );

    const solicitacoes_filtradas = computed(() => {
      if (filtro_ativo.value === "todos") return props.solicitacoes;
      return props.solicitacoes.filter((s) => s.status === filtro_ativo.value);
    });

    const notas     = ref({});
    const procesando = ref({});

    function inicial(usuario) {
      return (usuario?.name || "?").charAt(0).toUpperCase();
    }

    function chip_status(status) {
      return status_chips[status] ?? status_chips.pendente;
    }

    function rotulo_status(status) {
      return status_rotulos[status] ?? status;
    }

    function revisar(id, acao) {
      procesando.value[id] = true;
      const form = useForm({
        acao,
        admin_nota: notas.value[id] ?? null,
      });
      form.put(route("solicitacoes-autor.update", { solicitacao: id }), {
        preserveScroll: true,
        onFinish: () => {
          procesando.value[id] = false;
          delete notas.value[id];
        },
      });
    }

    return {
      filtro_ativo,
      filtros,
      pendentes,
      solicitacoes_filtradas,
      notas,
      procesando,
      inicial,
      chip_status,
      rotulo_status,
      revisar,
    };
  },

  computed: {
    flash_success() { return this.$page.props.flash?.success; },
    flash_error()   { return this.$page.props.flash?.error;   },
  },
};
</script>

