<template>
  <div class="min-h-screen bg-background text-on-background font-body flex flex-col">
    <Navbar />

    <main class="flex-grow w-full max-w-5xl mx-auto px-4 sm:px-8 py-28 pb-20">
      <header class="mb-10">
        <h1 class="font-display font-black text-3xl tracking-tight">Minha Conta</h1>
        <p class="text-on-surface-variant text-sm mt-1">
          Histórico de visualizações, comentários e perfil de leitor
        </p>
      </header>

      <!-- Mensagens flash -->
      <div
        v-if="mensagem_sucesso"
        class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
        role="status"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">check_circle</span>
        {{ mensagem_sucesso }}
      </div>
      <div
        v-if="mensagem_erro"
        class="mb-6 flex items-center gap-2 rounded-lg border border-error/30 bg-error/10 px-4 py-3 text-sm text-error"
        role="alert"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">error</span>
        {{ mensagem_erro }}
      </div>

      <!-- Perfil -->
      <section
        class="relative overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 md:p-8 shadow-2xl mb-8"
      >
        <div
          class="absolute -top-20 -right-20 w-56 h-56 bg-primary/20 rounded-full blur-[100px] pointer-events-none"
        ></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-5">
          <div class="flex items-center gap-5">
            <img
              v-if="usuario?.avatar_url || usuario?.avatar"
              :src="usuario.avatar_url || usuario.avatar"
              alt="Foto de perfil"
              class="w-16 h-16 rounded-full object-cover border border-outline-variant/30 shrink-0"
            />
            <div
              v-else
              class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center text-primary font-black text-xl shrink-0"
            >
              {{ inicial }}
            </div>
            <div class="min-w-0">
              <h2 class="text-xl font-bold truncate">{{ usuario?.name }}</h2>
              <p class="text-sm text-on-surface-variant truncate">{{ usuario?.email }}</p>
              <span
                class="mt-2 inline-block rounded-full bg-primary/20 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-primary"
              >
                Leitor
              </span>
            </div>
          </div>

          <Link
            href="/minha-conta/perfil"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface border border-outline-variant/30 px-4 py-2.5 text-xs font-semibold transition-all self-start sm:self-auto"
          >
            <span class="material-symbols-outlined text-sm">settings</span>
            <span>Editar Perfil</span>
          </Link>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Histórico de visualizações -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
        >
          <h3
            class="font-headline text-lg font-bold mb-5 flex items-center gap-2"
          >
            <span class="material-symbols-outlined text-primary">history</span>
            Histórico de Visualizações
          </h3>
          <p v-if="visualizacoes.length === 0" class="text-sm text-on-surface-variant">
            Você ainda não visualizou nenhum post.
          </p>
          <ul v-else class="space-y-4">
            <li
              v-for="visualizacao in visualizacoes"
              :key="visualizacao.id"
              class="flex items-center justify-between gap-3"
            >
              <Link
                :href="url_do_post(visualizacao.post)"
                class="text-sm font-medium text-on-surface hover:text-primary transition-colors truncate no-underline"
              >
                {{ visualizacao.post.title }}
              </Link>
              <span class="text-xs font-mono text-on-surface-variant shrink-0">
                {{ data_formatada(visualizacao.viewed_at) }}
              </span>
            </li>
          </ul>
        </section>

        <!-- Meus comentários (Gerenciáveis) -->
        <section
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between mb-5">
            <h3 class="font-headline text-lg font-bold flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">forum</span>
              Meus Comentários
            </h3>
            <span class="font-mono text-xs text-on-surface-variant">
              {{ comentarios.length }} comentário(s)
            </span>
          </div>

          <p v-if="comentarios.length === 0" class="text-sm text-on-surface-variant">
            Você ainda não comentou em nenhum post.
          </p>
          <ul v-else class="space-y-4">
            <li
              v-for="comentario in comentarios"
              :key="comentario.id"
              class="group relative rounded-lg border border-outline-variant/15 bg-surface-container-highest/30 p-3.5 transition-colors hover:border-outline-variant/30 hover:bg-surface-container-highest/50"
            >
              <div class="flex items-start justify-between gap-3">
                <p class="text-sm text-slate-200 line-clamp-3 leading-relaxed flex-1">
                  {{ comentario.content }}
                </p>
                <button
                  type="button"
                  class="shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-md text-on-surface-variant hover:text-error hover:bg-error/10 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error/60"
                  title="Excluir comentário"
                  @click="abrir_modal_exclusao_comentario(comentario)"
                >
                  <span class="material-symbols-outlined text-base">delete</span>
                </button>
              </div>
              <div class="mt-2.5 flex items-center justify-between text-xs">
                <Link
                  :href="url_do_post(comentario.post)"
                  class="text-primary font-bold no-underline hover:text-inverse-primary truncate max-w-[240px]"
                >
                  {{ comentario.post.title }}
                </Link>
                <span class="font-mono text-on-surface-variant shrink-0">
                  {{ data_formatada(comentario.created_at) }}
                </span>
              </div>
            </li>
          </ul>
        </section>
      </div>
    </main>

    <!-- Modal de Confirmação de Exclusão de Comentário -->
    <ModalConfirmacao
      :aberto="modal_exclusao_aberto"
      titulo="Excluir Comentário"
      mensagem="Tem certeza que deseja excluir seu comentário? Essa ação não pode ser desfeita."
      :item_nome="comentario_para_excluir?.content || ''"
      :processando="form_excluir.processing"
      rotulo_confirmar="Sim, excluir"
      rotulo_cancelar="Cancelar"
      @confirmar="confirmar_exclusao_comentario"
      @cancelar="cancelar_exclusao_comentario"
    />

    <Footer />
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import ModalConfirmacao from "@/Components/ModalConfirmacao.vue";

const props = defineProps({
    visualizacoes: {
        type: Array,
        default: () => [],
    },
    comentarios: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const usuario = computed(() => page.props.auth?.user ?? null);
const inicial = computed(() => (usuario.value?.name || "?").charAt(0).toUpperCase());
const mensagem_sucesso = computed(() => page.props.flash?.success);
const mensagem_erro = computed(() => page.props.flash?.error);

const form_excluir = useForm({});
const modal_exclusao_aberto = ref(false);
const comentario_para_excluir = ref(null);

function abrir_modal_exclusao_comentario(comentario) {
    comentario_para_excluir.value = comentario;
    modal_exclusao_aberto.value = true;
}

function cancelar_exclusao_comentario() {
    modal_exclusao_aberto.value = false;
    comentario_para_excluir.value = null;
}

function confirmar_exclusao_comentario() {
    if (!comentario_para_excluir.value) return;

    form_excluir.delete(route("comments.destroy", { comment: comentario_para_excluir.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            cancelar_exclusao_comentario();
        },
    });
}

const url_do_post = (post) => `/post/show/${post?.slug || ""}`;

const data_formatada = (valor) => {
    if (!valor) {
        return "";
    }
    return DateTime.fromISO(valor).setLocale("pt-BR").toFormat("dd LLL, yyyy");
};
</script>
