<script setup>
import { onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { use_consent } from '@/Composables/use_consent';
import { carregar_analytics, negar_analytics } from '@/lib/analytics';

/**
 * Componente invisível (sem template) responsável por aplicar o GA4 sob consentimento.
 */
const page = usePage();
const { consentimento } = use_consent();

function aplicar() {
    if (consentimento.value === 'denied') {
        negar_analytics();
        return;
    }

    if (consentimento.value !== 'granted') {
        return;
    }

    const id = page.props.analytics?.ga4Id ?? null;
    if (id) {
        carregar_analytics(id);
    }
}

onMounted(aplicar);
watch(consentimento, aplicar);
</script>

<template>
  <!-- Componente invisível: a tag é injetada no document.head pelo client-side -->
</template>
