<template>
  <div>
    <div
      class="flex min-h-[42px] flex-wrap items-center gap-2 rounded-lg border border-outline-variant/30 bg-surface-container-highest p-2 transition-colors focus-within:border-primary focus-within:ring-1 focus-within:ring-primary"
    >
      <span
        v-for="(tag, index) in model_value"
        :key="tag"
        class="inline-flex items-center gap-1 rounded bg-secondary-container px-2 py-1 text-xs font-medium text-secondary"
      >
        {{ tag }}
        <button
          type="button"
          class="text-secondary transition-colors hover:text-on-surface focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary/60"
          :aria-label="`Remover tag ${tag}`"
          @click="remover(index)"
        >
          <span class="material-symbols-outlined text-[14px]">close</span>
        </button>
      </span>
      <input
        v-model="texto"
        type="text"
        class="min-w-24 flex-1 bg-transparent p-0 text-sm text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none"
        placeholder="Adicionar tag..."
        aria-label="Adicionar tag"
        @focus="focado = true"
        @blur="fechar_sugestoes"
        @keydown.enter.prevent="adicionar_do_input"
        @keydown.down.prevent="navegar_sugestoes(1)"
        @keydown.up.prevent="navegar_sugestoes(-1)"
        @keydown.esc="fechar_sugestoes"
        @keydown.backspace="ao_backspace"
      />
    </div>

    <ul
      v-if="focado && sugestoes_filtradas.length"
      class="mt-1 overflow-hidden rounded-lg border border-outline-variant/20 bg-surface-container-high shadow-2xl"
      role="listbox"
      aria-label="Sugestões de tags"
    >
      <li v-for="(sugestao, index) in sugestoes_filtradas" :key="sugestao.slug" role="option" :aria-selected="index === indice_sugestao">
        <button
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary/60"
          :class="
            index === indice_sugestao
              ? 'bg-primary-container text-on-surface'
              : 'text-on-surface-variant hover:bg-secondary-container hover:text-on-surface'
          "
          @mousedown.prevent="adicionar(sugestao.name)"
        >
          <span class="material-symbols-outlined text-sm" aria-hidden="true">tag</span>
          {{ sugestao.name }}
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
import { computed, ref } from "vue";

export default {
    name: "TagInput",
    props: {
        model_value: { type: Array, default: () => [] },
        sugestoes: { type: Array, default: () => [] },
    },
    emits: ["update:model_value"],
    setup(props, { emit }) {
        const texto = ref("");
        const focado = ref(false);
        const indice_sugestao = ref(-1);

        const sugestoes_filtradas = computed(() => {
            const ja_adicionadas = new Set(props.model_value.map((tag) => tag.toLowerCase().trim()));
            const consulta = texto.value.trim().toLowerCase();
            const lista = props.sugestoes.filter((sugestao) => {
                const nome = sugestao.name.toLowerCase();
                return !ja_adicionadas.has(nome) && (!consulta || nome.includes(consulta));
            });
            return lista.slice(0, 8);
        });

        function atualizar_sugestoes(valor) {
            emit("update:model_value", valor);
        }

        function remover(index) {
            const nova_lista = [...props.model_value];
            nova_lista.splice(index, 1);
            atualizar_sugestoes(nova_lista);
        }

        function adicionar(nome) {
            const limpo = nome.trim();
            if (!limpo) {
                return;
            }
            const existe = props.model_value.some(
                (tag) => tag.toLowerCase() === limpo.toLowerCase()
            );
            if (!existe) {
                atualizar_sugestoes([...props.model_value, limpo]);
            }
            texto.value = "";
            indice_sugestao.value = -1;
        }

        function adicionar_do_input() {
            if (indice_sugestao.value >= 0 && sugestoes_filtradas.value[indice_sugestao.value]) {
                adicionar(sugestoes_filtradas.value[indice_sugestao.value].name);
            } else if (texto.value.trim()) {
                adicionar(texto.value);
            }
        }

        function navegar_sugestoes(direcao) {
            const total = sugestoes_filtradas.value.length;
            if (!total) {
                return;
            }
            indice_sugestao.value =
                (indice_sugestao.value + direcao + total) % total;
        }

        function fechar_sugestoes() {
            focado.value = false;
            indice_sugestao.value = -1;
        }

        function ao_backspace() {
            if (!texto.value && props.model_value.length) {
                remover(props.model_value.length - 1);
            }
        }

        return {
            texto,
            focado,
            indice_sugestao,
            sugestoes_filtradas,
            remover,
            adicionar,
            adicionar_do_input,
            navegar_sugestoes,
            fechar_sugestoes,
            ao_backspace,
        };
    },
};
</script>
