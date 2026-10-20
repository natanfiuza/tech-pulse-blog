<script setup>
import { computed } from 'vue';
import { use_consent } from '@/Composables/use_consent';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const { consentimento, banner_aberto, aceitar, recusar } = use_consent();

const medicao_marcada = computed(() => {
    return consentimento.value === 'granted';
});

function alternar_medicao(marcado) {
    if (marcado) {
        aceitar();
    } else {
        recusar();
    }
}
</script>

<template>
  <aside
    v-if="banner_aberto"
    data-test="consent-banner"
    role="region"
    aria-label="Preferências de Cookies e Privacidade"
    class="fixed inset-x-4 bottom-4 z-50 flex flex-col gap-3 rounded-xl border border-outline-variant/30 bg-surface-container-low p-5 text-sm text-on-surface shadow-2xl backdrop-blur-md sm:inset-x-auto sm:left-6 sm:w-[420px]"
  >
    <div class="flex items-start gap-3">
      <span class="material-symbols-outlined mt-0.5 text-2xl text-primary shrink-0 select-none">
        cookie
      </span>
      <div class="flex flex-col gap-1">
        <h2 class="font-headline font-black text-base tracking-tight text-on-surface">
          Cookies e Privacidade
        </h2>
        <p class="text-xs text-on-surface-variant leading-relaxed">
          Utilizamos cookies essenciais para o funcionamento do blog e medição anônima de audiência (páginas visualizadas) para melhorar nosso conteúdo. Nada de anúncios ou rastreamento entre sites.
        </p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 pt-1">
      <PrimaryButton
        data-test="consent-accept"
        class="!text-xs !py-2 !px-4"
        @click="aceitar"
      >
        Aceitar
      </PrimaryButton>
      <SecondaryButton
        data-test="consent-reject"
        class="!text-xs !py-2 !px-4"
        @click="recusar"
      >
        Recusar
      </SecondaryButton>
    </div>

    <details data-test="consent-details" class="group mt-1 text-xs">
      <summary class="cursor-pointer text-on-surface-variant hover:text-on-surface transition-colors select-none font-bold">
        Personalizar preferências
      </summary>

      <div class="mt-3 flex flex-col gap-3 border-t border-outline-variant/20 pt-3">
        <div class="flex items-start gap-2.5">
          <input
            type="checkbox"
            checked
            disabled
            class="mt-0.5 rounded border-gray-600 bg-surface-container-highest text-primary focus:ring-0 cursor-not-allowed opacity-75"
          />
          <div class="flex flex-col">
            <span class="font-bold text-on-surface">Essenciais</span>
            <span class="text-[11px] text-on-surface-variant">
              Necessários para navegação, autenticação e sessão. Não podem ser desativados.
            </span>
          </div>
        </div>

        <label class="flex items-start gap-2.5 cursor-pointer">
          <Checkbox
            :checked="medicao_marcada"
            class="mt-0.5"
            @update:checked="alternar_medicao"
          />
          <span class="flex flex-col">
            <span class="font-bold text-on-surface">Medição de Audiência</span>
            <span class="text-[11px] text-on-surface-variant">
              Google Analytics (GA4) anônimo para entender quais posts são mais lidos.
            </span>
          </span>
        </label>
      </div>
    </details>
  </aside>
</template>
