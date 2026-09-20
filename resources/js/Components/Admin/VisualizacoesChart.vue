<template>
  <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="font-headline text-base font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary" aria-hidden="true">monitoring</span>
          Visualizações dos Posts (Últimos 30 dias)
        </h2>
        <p class="mt-1 text-xs text-on-surface-variant">
          Histórico diário de acessos aos seus artigos publicados
        </p>
      </div>

      <!-- Resumo rápido -->
      <div class="flex items-center gap-4">
        <div class="rounded-lg bg-surface-container-high/70 border border-outline-variant/20 px-3.5 py-1.5 text-right">
          <span class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold block">Total no período</span>
          <span class="font-mono text-base font-bold text-primary">{{ total_periodo }}</span>
        </div>
        <div class="rounded-lg bg-surface-container-high/70 border border-outline-variant/20 px-3.5 py-1.5 text-right">
          <span class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold block">Média diária</span>
          <span class="font-mono text-base font-bold text-on-surface">{{ media_diaria }}</span>
        </div>
      </div>
    </div>

    <!-- Gráfico SVG interativo -->
    <div class="relative w-full h-64 select-none" ref="container_grafico">
      <svg
        class="w-full h-full overflow-visible"
        viewBox="0 0 800 240"
        preserveAspectRatio="none"
        @mousemove="ao_mover_mouse"
        @mouseleave="ao_sair_mouse"
      >
        <defs>
          <linearGradient id="gradiente-area-views" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#2b52ee" stop-opacity="0.35" />
            <stop offset="100%" stop-color="#2b52ee" stop-opacity="0.0" />
          </linearGradient>
          <filter id="brilho-linha" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="3" result="blur" />
            <feComposite in="SourceGraphic" in2="blur" operator="over" />
          </filter>
        </defs>

        <!-- Linhas de grade horizontais -->
        <g class="stroke-outline-variant/15 stroke-dasharray-2">
          <line x1="0" y1="40" x2="800" y2="40" stroke="currentColor" stroke-dasharray="3 3" />
          <line x1="0" y1="100" x2="800" y2="100" stroke="currentColor" stroke-dasharray="3 3" />
          <line x1="0" y1="160" x2="800" y2="160" stroke="currentColor" stroke-dasharray="3 3" />
          <line x1="0" y1="220" x2="800" y2="220" stroke="currentColor" />
        </g>

        <!-- Área preenchida -->
        <path
          v-if="caminho_area"
          :d="caminho_area"
          fill="url(#gradiente-area-views)"
        />

        <!-- Linha do gráfico -->
        <path
          v-if="caminho_linha"
          :d="caminho_linha"
          fill="none"
          stroke="#2b52ee"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="transition-all duration-300"
        />

        <!-- Pontos nos dados -->
        <circle
          v-for="(ponto, indice) in pontos_coordenadas"
          :key="indice"
          :cx="ponto.x"
          :cy="ponto.y"
          r="3"
          class="fill-primary stroke-surface transition-all duration-150 hover:r-5 cursor-pointer"
          :class="ponto_ativo === indice ? 'r-5 stroke-2 stroke-white fill-secondary' : ''"
        />

        <!-- Linha vertical do indicador ativo -->
        <line
          v-if="ponto_selecionado"
          :x1="ponto_selecionado.x"
          y1="10"
          :x2="ponto_selecionado.x"
          y2="220"
          stroke="#6272b4"
          stroke-width="1.5"
          stroke-dasharray="4 4"
          class="pointer-events-none opacity-75"
        />
      </svg>

      <!-- Tooltip flutuante -->
      <div
        v-if="ponto_selecionado"
        class="pointer-events-none absolute z-20 -translate-x-1/2 -translate-y-full rounded-lg border border-primary/40 bg-surface-container-highest px-3 py-1.5 shadow-xl transition-all duration-75"
        :style="{ left: `${ponto_selecionado.porcentagem_x}%`, top: `${ponto_selecionado.porcentagem_y - 12}%` }"
      >
        <p class="font-mono text-[11px] text-on-surface-variant">{{ ponto_selecionado.data.label }} ({{ ponto_selecionado.data.data }})</p>
        <p class="font-headline text-xs font-bold text-on-surface">
          <span class="text-primary font-mono text-sm font-extrabold">{{ ponto_selecionado.data.total }}</span> visualizações
        </p>
      </div>
    </div>

    <!-- Eixo X (Datas) -->
    <div class="mt-3 flex justify-between text-[11px] font-mono text-on-surface-variant/70 px-1">
      <span v-for="(rotulo, index) in rotulos_eixo_x" :key="index">
        {{ rotulo }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    dados: {
        type: Array,
        default: () => [],
    },
});

const ponto_ativo = ref(null);
const container_grafico = ref(null);

const total_periodo = computed(() => {
    return props.dados.reduce((acumulado, atual) => acumulado + (atual.total || 0), 0);
});

const media_diaria = computed(() => {
    if (props.dados.length === 0) return "0";
    const media = total_periodo.value / props.dados.length;
    return media.toFixed(1);
});

const valor_maximo = computed(() => {
    const maximo = Math.max(...props.dados.map((d) => d.total || 0), 0);
    return maximo === 0 ? 10 : Math.ceil(maximo * 1.2);
});

const pontos_coordenadas = computed(() => {
    if (props.dados.length === 0) return [];
    const largura = 800;
    const altura = 200;
    const padding_topo = 20;
    const total_pontos = props.dados.length;
    const espaco_x = largura / Math.max(total_pontos - 1, 1);

    return props.dados.map((item, index) => {
        const x = index * espaco_x;
        const proporcao = (item.total || 0) / valor_maximo.value;
        const y = padding_topo + (altura - proporcao * altura);
        return {
            x,
            y,
            porcentagem_x: (x / largura) * 100,
            porcentagem_y: (y / (altura + padding_topo + 20)) * 100,
            data: item,
            indice: index,
        };
    });
});

const caminho_linha = computed(() => {
    if (pontos_coordenadas.value.length === 0) return "";
    return pontos_coordenadas.value.reduce((acumulado, ponto, index) => {
        if (index === 0) {
            return `M ${ponto.x},${ponto.y}`;
        }
        const anterior = pontos_coordenadas.value[index - 1];
        const ponto_controle_x1 = anterior.x + (ponto.x - anterior.x) / 2;
        const ponto_controle_y1 = anterior.y;
        const ponto_controle_x2 = anterior.x + (ponto.x - anterior.x) / 2;
        const ponto_controle_y2 = ponto.y;
        return `${acumulado} C ${ponto_controle_x1},${ponto_controle_y1} ${ponto_controle_x2},${ponto_controle_y2} ${ponto.x},${ponto.y}`;
    }, "");
});

const caminho_area = computed(() => {
    if (pontos_coordenadas.value.length === 0) return "";
    const linha = caminho_linha.value;
    const primeiro = pontos_coordenadas.value[0];
    const ultimo = pontos_coordenadas.value[pontos_coordenadas.value.length - 1];
    return `${linha} L ${ultimo.x},220 L ${primeiro.x},220 Z`;
});

const ponto_selecionado = computed(() => {
    if (ponto_ativo.value === null || !pontos_coordenadas.value[ponto_ativo.value]) {
        return null;
    }
    return pontos_coordenadas.value[ponto_ativo.value];
});

const rotulos_eixo_x = computed(() => {
    if (props.dados.length === 0) return [];
    const tamanho = props.dados.length;
    const indices = [
        0,
        Math.floor(tamanho * 0.25),
        Math.floor(tamanho * 0.5),
        Math.floor(tamanho * 0.75),
        tamanho - 1,
    ];
    return indices.map((i) => props.dados[i]?.label || "");
});

function ao_mover_mouse(evento) {
    if (!container_grafico.value || pontos_coordenadas.value.length === 0) return;
    const rect = container_grafico.value.getBoundingClientRect();
    const pos_x = evento.clientX - rect.left;
    const proporcao_x = Math.max(0, Math.min(1, pos_x / rect.width));
    const indice = Math.round(proporcao_x * (pontos_coordenadas.value.length - 1));
    ponto_ativo.value = indice;
}

function ao_sair_mouse() {
    ponto_ativo.value = null;
}
</script>

