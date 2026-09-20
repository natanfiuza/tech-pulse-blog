<template>
  <div class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30 flex flex-col">
    <Navbar :categorias="[]" />

    <main class="flex-1 pt-28 md:pt-36 pb-20 px-4 sm:px-8 max-w-xl mx-auto w-full">
      <div class="bg-surface-container-high border border-outline-variant/30 rounded-2xl p-6 sm:p-10 shadow-2xl">
        <!-- Estado: Sucesso / Concluído -->
        <div v-if="status === 'done'" class="text-center space-y-6">
          <div class="w-16 h-16 bg-primary/20 text-primary rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">check_circle</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_cancel_done_title || "Inscrição Cancelada" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_cancel_done_desc || "Você não receberá mais os e-mails da newsletter TechPulse. Sentiremos sua falta!" }}
          </p>
          <div class="pt-4">
            <Link
              href="/"
              class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-lg text-sm glow-hover transition-all no-underline"
            >
              <span class="material-symbols-outlined text-base">arrow_back</span>
              {{ ui_strings.ui_back_to_home || "Voltar para o Início" }}
            </Link>
          </div>
        </div>

        <!-- Estado: Já Cancelado -->
        <div v-else-if="status === 'already_canceled'" class="text-center space-y-6">
          <div class="w-16 h-16 bg-amber-500/20 text-amber-400 rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">info</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_cancel_already_title || "Inscrição já cancelada" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_cancel_already_desc || "Este e-mail já estava cancelado em nossa lista de envio." }}
          </p>
          <div class="pt-4">
            <Link
              href="/"
              class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-lg text-sm glow-hover transition-all no-underline"
            >
              {{ ui_strings.ui_back_to_home || "Voltar para o Início" }}
            </Link>
          </div>
        </div>

        <!-- Estado: Não Encontrado -->
        <div v-else-if="status === 'not_found'" class="text-center space-y-6">
          <div class="w-16 h-16 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">error_outline</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_cancel_not_found_title || "Link Inválido" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_cancel_not_found_desc || "Assinante não encontrado ou link inválido." }}
          </p>
          <div class="pt-4">
            <Link
              href="/"
              class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-lg text-sm glow-hover transition-all no-underline"
            >
              {{ ui_strings.ui_back_to_home || "Voltar para o Início" }}
            </Link>
          </div>
        </div>

        <!-- Estado: Formulário de Confirmação de Cancelamento -->
        <div v-else class="text-center space-y-6">
          <div class="w-16 h-16 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">unsubscribe</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_cancel_title || "Cancelar Inscrição" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_cancel_confirm_question || "Tem certeza de que deseja parar de receber a newsletter semanal do TechPulse?" }}
          </p>
          <p v-if="subscriber?.email" class="font-mono text-xs text-primary bg-primary/10 py-1.5 px-3 rounded inline-block">
            {{ subscriber.email }}
          </p>

          <form class="pt-4 space-y-3" @submit.prevent="submit_cancel">
            <button
              type="submit"
              :disabled="canceling"
              class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-3.5 rounded-lg text-sm transition-all disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
            >
              <span v-if="canceling" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
              <span>{{ canceling ? (ui_strings.ui_cancel_submitting || "Cancelando...") : (ui_strings.ui_cancel_submit || "Confirmar Cancelamento") }}</span>
            </button>
            <Link
              href="/"
              class="block text-xs text-on-surface-variant hover:text-white transition-colors no-underline pt-2"
            >
              {{ ui_strings.ui_back_to_home || "Voltar sem cancelar" }}
            </Link>
          </form>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  subscriber: {
    type: Object,
    default: () => ({}),
  },
  current_lang: {
    type: String,
    default: "pt_br",
  },
  ui_strings: {
    type: Object,
    default: () => ({}),
  },
});

const canceling = ref(false);

const submit_cancel = () => {
  if (!props.subscriber?.uuid) {
    return;
  }
  canceling.value = true;
  router.post(
    route("newsletter.unsubscribe", { uuid: props.subscriber.uuid }),
    {},
    {
      onFinish: () => {
        canceling.value = false;
      },
    }
  );
};
</script>

