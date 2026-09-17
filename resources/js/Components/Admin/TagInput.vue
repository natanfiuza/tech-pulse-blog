<template>
  <div>
    <div
      class="flex min-h-[42px] flex-wrap items-center gap-2 rounded-lg border border-outline-variant/30 bg-surface-container-highest p-2 transition-colors focus-within:border-primary focus-within:ring-1 focus-within:ring-primary"
    >
      <span
        v-for="(tag, index) in tags_ativas"
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
        @blur="ao_perder_foco"
        @input="ao_input"
        @paste="ao_colar"
        @keydown.enter.prevent="adicionar_do_input"
        @keydown.tab="ao_tab"
        @keydown="ao_keydown_delimitador"
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
import { computed, ref, watch } from "vue";

export default {
    name: "TagInput",
    props: {
        model_value: { type: Array, default: () => [] },
        modelValue: { type: Array, default: () => [] },
        sugestoes: { type: Array, default: () => [] },
        texto_pendente: { type: String, default: "" },
    },
    emits: ["update:model_value", "update:modelValue", "update:texto_pendente"],
    setup(props, { emit, expose }) {
        const tags_ativas = computed(() => {
            if (Array.isArray(props.model_value) && props.model_value.length > 0) {
                return props.model_value;
            }
            if (Array.isArray(props.modelValue) && props.modelValue.length > 0) {
                return props.modelValue;
            }
            return props.model_value || props.modelValue || [];
        });

        const texto = ref(props.texto_pendente || "");
        const focado = ref(false);
        const indice_sugestao = ref(-1);

        watch(
            () => props.texto_pendente,
            (novo_valor) => {
                if (novo_valor !== undefined && novo_valor !== texto.value) {
                    texto.value = novo_valor;
                }
            }
        );

        watch(texto, (novo_texto) => {
            emit("update:texto_pendente", novo_texto);
        });

        const sugestoes_filtradas = computed(() => {
            const ja_adicionadas = new Set(
                tags_ativas.value.map((tag) => String(tag).toLowerCase().trim())
            );
            const consulta = texto.value.trim().toLowerCase();
            const lista = props.sugestoes.filter((sugestao) => {
                const nome = sugestao.name.toLowerCase();
                return !ja_adicionadas.has(nome) && (!consulta || nome.includes(consulta));
            });
            return lista.slice(0, 8);
        });

        function atualizar_sugestoes(valor) {
            emit("update:model_value", valor);
            emit("update:modelValue", valor);
        }

        function remover(index) {
            const nova_lista = [...tags_ativas.value];
            nova_lista.splice(index, 1);
            atualizar_sugestoes(nova_lista);
        }

        function adicionar(valor) {
            if (!valor) {
                return;
            }

            const partes = Array.isArray(valor)
                ? valor
                : String(valor).split(/[,;\n]/);

            const nova_lista = [...tags_ativas.value];
            let alterou = false;

            for (const item of partes) {
                const limpo = item.trim();
                if (!limpo) {
                    continue;
                }
                const existe = nova_lista.some(
                    (tag) => String(tag).toLowerCase() === limpo.toLowerCase()
                );
                if (!existe) {
                    nova_lista.push(limpo);
                    alterou = true;
                }
            }

            if (alterou) {
                atualizar_sugestoes(nova_lista);
            }

            texto.value = "";
            indice_sugestao.value = -1;
        }

        function confirmar_texto() {
            if (texto.value.trim()) {
                adicionar(texto.value);
            }
        }

        function adicionar_do_input() {
            if (indice_sugestao.value >= 0 && sugestoes_filtradas.value[indice_sugestao.value]) {
                adicionar(sugestoes_filtradas.value[indice_sugestao.value].name);
            } else if (texto.value.trim()) {
                adicionar(texto.value);
            }
        }

        function ao_keydown_delimitador(evento) {
            if (evento.key === "," || evento.key === ";") {
                evento.preventDefault();
                if (texto.value.trim()) {
                    adicionar(texto.value);
                }
            }
        }

        function ao_tab(evento) {
            if (texto.value.trim() || indice_sugestao.value >= 0) {
                evento.preventDefault();
                adicionar_do_input();
            }
        }

        function ao_input() {
            if (texto.value.includes(",") || texto.value.includes(";") || texto.value.includes("\n")) {
                adicionar(texto.value);
            }
        }

        function ao_colar(evento) {
            const texto_colado = evento.clipboardData?.getData("text") || "";
            if (texto_colado.includes(",") || texto_colado.includes(";") || texto_colado.includes("\n")) {
                evento.preventDefault();
                adicionar(texto_colado);
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

        function ao_perder_foco() {
            confirmar_texto();
            fechar_sugestoes();
        }

        function ao_backspace() {
            if (!texto.value && tags_ativas.value.length) {
                remover(tags_ativas.value.length - 1);
            }
        }

        expose({
            confirmar_texto,
            adicionar,
            remover,
        });

        return {
            texto,
            focado,
            indice_sugestao,
            sugestoes_filtradas,
            tags_ativas,
            remover,
            adicionar,
            confirmar_texto,
            adicionar_do_input,
            ao_keydown_delimitador,
            ao_tab,
            ao_input,
            ao_colar,
            navegar_sugestoes,
            fechar_sugestoes,
            ao_perder_foco,
            ao_backspace,
        };
    },
};
</script>
