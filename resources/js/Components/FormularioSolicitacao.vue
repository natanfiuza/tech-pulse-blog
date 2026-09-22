<template>
  <form class="space-y-6" @submit.prevent="$emit('submitted')">
    <div>
      <label for="motivacao" class="block font-headline font-bold text-sm text-on-surface mb-2">
        Por que você quer se tornar autor?
        <span class="text-on-surface-variant font-normal">(mín. 50 caracteres)</span>
      </label>
      <textarea
        id="motivacao"
        :value="motivacao"
        @input="$emit('update:motivacao', $event.target.value)"
        rows="6"
        required
        minlength="50"
        maxlength="2000"
        placeholder="Descreva sua experiência, os temas que pretende escrever e o que o TechPulse ganha com sua contribuição..."
        class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50 resize-y"
      ></textarea>
      <div class="flex items-center justify-between mt-1">
        <p v-if="erros?.motivacao" class="text-error text-sm">{{ erros.motivacao }}</p>
        <p v-else class="text-xs text-on-surface-variant/60">{{ motivacao?.length ?? 0 }}/2000</p>
      </div>
    </div>

    <div class="rounded-lg border border-outline-variant/20 bg-surface-container-high/50 p-4 text-sm text-on-surface-variant space-y-1">
      <p class="flex items-center gap-2 font-medium text-on-surface">
        <span class="material-symbols-outlined text-base text-primary" aria-hidden="true">info</span>
        O que acontece depois?
      </p>
      <p>Sua solicitação será revisada pela equipe do TechPulse. Após aprovação, seu perfil será promovido a autor e você receberá acesso ao painel de publicação.</p>
    </div>

    <button
      type="submit"
      :disabled="processando"
      class="w-full bg-primary hover:bg-surface-tint text-on-primary font-medium py-3 px-4 rounded-lg glow-hover transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2"
    >
      <span class="material-symbols-outlined text-base" aria-hidden="true">send</span>
      {{ processando ? 'Enviando...' : 'Enviar Solicitação' }}
    </button>
  </form>
</template>

<script setup>
defineProps({
  motivacao:   { type: String,  default: "" },
  processando: { type: Boolean, default: false },
  erros:       { type: Object,  default: () => ({}) },
});

defineEmits(["submitted", "update:motivacao"]);
</script>

