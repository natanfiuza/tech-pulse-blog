<template>
  <div>
    <!-- Estado: Confirmação enviada -->
    <div
      v-if="flash_status === 'sent'"
      class="p-4 rounded-lg bg-primary/10 border border-primary/30 text-slate-800 dark:text-slate-200"
    >
      <div class="flex items-center gap-2 text-primary font-bold mb-1">
        <span class="material-symbols-outlined text-xl">mark_email_read</span>
        <span>E-mail Enviado!</span>
      </div>
      <p class="text-xs text-on-surface-variant leading-relaxed">
        {{ ui_strings.ui_subscribe_sent || "Enviamos um e-mail com o link para confirmar sua inscrição. Verifique sua caixa de entrada!" }}
      </p>
    </div>

    <!-- Estado: Link de cancelamento enviado -->
    <div
      v-else-if="flash_status === 'cancel_link_sent'"
      class="p-4 rounded-lg bg-primary/10 border border-primary/30 text-slate-800 dark:text-slate-200"
    >
      <div class="flex items-center gap-2 text-primary font-bold mb-1">
        <span class="material-symbols-outlined text-xl">mail</span>
        <span>Link Enviado</span>
      </div>
      <p class="text-xs text-on-surface-variant leading-relaxed">
        {{ ui_strings.ui_subscribe_cancel_sent || "Se o e-mail estiver cadastrado, enviamos o link para cancelamento." }}
      </p>
    </div>

    <!-- Estado: E-mail já inscrito -->
    <div
      v-else-if="flash_status === 'already_confirmed'"
      class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/30 text-slate-800 dark:text-slate-200 space-y-3"
    >
      <div class="flex items-center gap-2 text-amber-400 font-bold">
        <span class="material-symbols-outlined text-xl">info</span>
        <span>Já Inscrito</span>
      </div>
      <p class="text-xs text-on-surface-variant leading-relaxed">
        {{ ui_strings.ui_subscribe_already_confirmed || "Este e-mail já está inscrito em nossa newsletter." }}
      </p>
      <button
        type="button"
        :disabled="cancel_link_sending"
        class="w-full text-xs text-amber-600 dark:text-amber-300 hover:text-amber-700 dark:hover:text-amber-200 font-semibold underline disabled:opacity-50 text-left transition-colors cursor-pointer"
        @click="send_cancel_link"
      >
        {{ cancel_link_sending ? "Enviando..." : (ui_strings.ui_subscribe_send_cancel || "Enviar link de cancelamento") }}
      </button>
    </div>

    <!-- Estado padrão: Formulário de Inscrição -->
    <div v-else class="space-y-4">
      <p class="text-sm text-on-surface-variant leading-relaxed">
        {{ ui_strings.ui_subscribe_desc || "Receba os pulsos de tecnologia e hacks de programação diretamente no seu e-mail, toda segunda-feira." }}
      </p>
      <form class="space-y-4" @submit.prevent="submit_newsletter">
        <div>
          <label for="newsletter_box_email" class="sr-only">Seu e-mail</label>
          <input
            id="newsletter_box_email"
            v-model="form.email"
            type="email"
            required
            :placeholder="ui_strings.ui_subscribe_placeholder || 'seu@email.com'"
            class="w-full bg-surface-dim border border-outline-variant/30 rounded-lg px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-on-surface-variant/50 text-slate-900 dark:text-slate-100"
          />
          <InputError class="mt-1" :message="form.errors.email" />
        </div>
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-primary text-white font-bold py-3 rounded-lg text-sm glow-hover transition-all disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
        >
          <span v-if="form.processing" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
          <span>{{ form.processing ? (ui_strings.ui_subscribe_sending || "Enviando...") : (ui_strings.ui_subscribe_button || "Inscrever Agora") }}</span>
        </button>
      </form>
      <p class="text-[10px] text-on-surface-variant/50 text-center">
        Respeitamos sua privacidade. Cancele a qualquer momento.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  current_lang: {
    type: String,
    default: "pt_br",
  },
  ui_strings: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  email: "",
  lang: props.current_lang,
});

const page = usePage();
const flash_status = computed(() => page.props.flash?.newsletter?.status ?? null);
const cancel_link_sending = ref(false);

const submit_newsletter = () => {
  form.lang = props.current_lang;
  form.post(route("newsletter.subscribe"), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset("email");
    },
  });
};

const send_cancel_link = () => {
  const email = page.props.flash?.newsletter?.email ?? form.email;
  cancel_link_sending.value = true;
  router.post(
    route("newsletter.send-cancel-link"),
    {
      email,
      lang: props.current_lang,
    },
    {
      preserveScroll: true,
      onFinish: () => {
        cancel_link_sending.value = false;
        form.reset("email");
      },
    }
  );
};
</script>

