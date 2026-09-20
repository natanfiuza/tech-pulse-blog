<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="aberto"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="id_titulo"
        @click.self="ao_clicar_backdrop"
      >
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-2"
        >
          <div
            v-if="aberto"
            class="w-full max-w-md rounded-2xl border border-slate-200 dark:border-outline-variant/30 bg-white dark:bg-surface-container-low shadow-2xl p-6 sm:p-7 space-y-5 transition-all text-slate-900 dark:text-on-surface"
          >
            <!-- Cabeçalho com Ícone de Alerta -->
            <div class="flex items-start gap-4">
              <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-error/10 border border-error/20 text-error">
                <span class="material-symbols-outlined text-2xl" aria-hidden="true">warning</span>
              </div>
              <div class="space-y-1 min-w-0 flex-1">
                <h3 :id="id_titulo" class="text-lg font-bold leading-snug font-display text-slate-900 dark:text-on-surface">
                  {{ titulo }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-on-surface-variant leading-relaxed">
                  {{ mensagem }}
                </p>
              </div>
            </div>

            <!-- Item Destacado (se fornecido) -->
            <div
              v-if="item_nome"
              class="rounded-lg border border-slate-200 dark:border-outline-variant/20 bg-slate-50 dark:bg-surface-container px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 dark:text-on-surface truncate"
            >
              {{ item_nome }}
            </div>

            <!-- Aviso Extra Contextual (se fornecido) -->
            <div
              v-if="aviso_extra"
              class="flex items-start gap-2.5 rounded-lg border border-amber-500/30 bg-amber-500/10 p-3 text-xs text-amber-800 dark:text-amber-300 leading-relaxed"
            >
              <span class="material-symbols-outlined text-base shrink-0 text-amber-600 dark:text-amber-400 mt-0.5">info</span>
              <span>{{ aviso_extra }}</span>
            </div>

            <!-- Ações do Rodapé -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 pt-2">
              <button
                type="button"
                :disabled="processando"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-outline-variant/30 bg-white dark:bg-surface px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-on-surface hover:bg-slate-100 dark:hover:bg-surface-container transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
                @click="ao_cancelar"
              >
                {{ rotulo_cancelar }}
              </button>

              <button
                type="button"
                :disabled="processando"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition-all duration-200 hover:bg-red-500 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/60"
                @click="ao_confirmar"
              >
                <span
                  v-if="processando"
                  class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
                  aria-hidden="true"
                ></span>
                <span v-else class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                <span>{{ processando ? "Excluindo..." : rotulo_confirmar }}</span>
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
  aberto: {
    type: Boolean,
    default: false,
  },
  titulo: {
    type: String,
    default: "Confirmar Exclusão",
  },
  mensagem: {
    type: String,
    default: "Tem certeza que deseja excluir este item? Essa ação não pode ser desfeita.",
  },
  item_nome: {
    type: String,
    default: "",
  },
  aviso_extra: {
    type: String,
    default: "",
  },
  rotulo_confirmar: {
    type: String,
    default: "Excluir",
  },
  rotulo_cancelar: {
    type: String,
    default: "Cancelar",
  },
  processando: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["confirmar", "cancelar"]);

const id_titulo = computed(() => `modal_titulo_${Math.random().toString(36).slice(2, 9)}`);

const ao_cancelar = () => {
  if (!props.processando) {
    emit("cancelar");
  }
};

const ao_confirmar = () => {
  emit("confirmar");
};

const ao_clicar_backdrop = () => {
  ao_cancelar();
};

const ao_pressionar_tecla = (evento) => {
  if (evento.key === "Escape" && props.aberto) {
    ao_cancelar();
  }
};

watch(
  () => props.aberto,
  (esta_aberto) => {
    if (typeof document !== "undefined") {
      if (esta_aberto) {
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "";
      }
    }
  }
);

onMounted(() => {
  if (typeof window !== "undefined") {
    window.addEventListener("keydown", ao_pressionar_tecla);
  }
});

onUnmounted(() => {
  if (typeof window !== "undefined") {
    window.removeEventListener("keydown", ao_pressionar_tecla);
    document.body.style.overflow = "";
  }
});
</script>

