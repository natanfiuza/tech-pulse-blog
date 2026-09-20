<template>
  <div class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30 flex flex-col">
    <Navbar :categorias="[]" />

    <main class="flex-1 pt-28 md:pt-36 pb-20 px-4 sm:px-8 max-w-xl mx-auto w-full">
      <div class="bg-surface-container-high border border-outline-variant/30 rounded-2xl p-6 sm:p-10 shadow-2xl">
        <!-- Estado: Já Confirmado -->
        <div v-if="status === 'already_confirmed'" class="text-center space-y-6">
          <div class="w-16 h-16 bg-primary/20 text-primary rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">verified</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_confirm_already_confirmed_title || "Inscrição Confirmada!" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_confirm_already_confirmed_desc || "Seu e-mail está ativo e você receberá as próximas edições da newsletter toda segunda-feira." }}
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

        <!-- Estado: Expirado -->
        <div v-else-if="status === 'expired'" class="text-center space-y-6">
          <div class="w-16 h-16 bg-amber-500/20 text-amber-400 rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">schedule</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_confirm_expired_title || "Link Expirado" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_confirm_expired_desc || "Este link expirou porque foi gerado há mais de 24 horas. Por favor, solicite uma nova inscrição." }}
          </p>
          <div class="pt-4">
            <Link
              href="/"
              class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-lg text-sm glow-hover transition-all no-underline"
            >
              {{ ui_strings.ui_back_to_home || "Ir para a Página Inicial" }}
            </Link>
          </div>
        </div>

        <!-- Estado: Não Encontrado -->
        <div v-else-if="status === 'not_found'" class="text-center space-y-6">
          <div class="w-16 h-16 bg-red-500/20 text-red-400 rounded-full flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">error_outline</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black">
            {{ ui_strings.ui_confirm_not_found_title || "Link Inválido" }}
          </h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            {{ ui_strings.ui_confirm_not_found_desc || "Não encontramos o pedido de confirmação para este link." }}
          </p>
          <div class="pt-4">
            <Link
              href="/"
              class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-lg text-sm glow-hover transition-all no-underline"
            >
              {{ ui_strings.ui_back_to_home || "Ir para a Página Inicial" }}
            </Link>
          </div>
        </div>

        <!-- Estado: Formulário de Confirmação -->
        <div v-else class="space-y-6">
          <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-primary/20 text-primary rounded-xl flex items-center justify-center mx-auto mb-3">
              <span class="material-symbols-outlined text-2xl">mark_email_unread</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">
              {{ ui_strings.ui_confirm_title || "Confirmar Inscrição" }}
            </h1>
            <p class="text-on-surface-variant text-xs sm:text-sm">
              {{ ui_strings.ui_confirm_desc || "Complete seus dados para receber as edições semanais do TechPulse toda segunda-feira." }}
            </p>
          </div>

          <form class="space-y-4" @submit.prevent="submit_confirm">
            <!-- Nome -->
            <div>
              <label for="subscriber_name" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                {{ ui_strings.ui_confirm_name_label || "Nome Completo" }} <span class="text-red-400">*</span>
              </label>
              <input
                id="subscriber_name"
                v-model="form.name"
                type="text"
                required
                :placeholder="ui_strings.ui_confirm_name_placeholder || 'Seu nome'"
                class="w-full bg-surface-dim border border-outline-variant/30 rounded-lg px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-slate-100 placeholder:text-on-surface-variant/40"
              />
              <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <!-- E-mail (readonly) -->
            <div>
              <label for="subscriber_email" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                {{ ui_strings.ui_confirm_email_label || "E-mail" }}
              </label>
              <input
                id="subscriber_email"
                v-model="form.email"
                type="email"
                readonly
                class="w-full bg-surface-dim/60 border border-outline-variant/20 rounded-lg px-4 py-3 text-sm text-slate-400 cursor-not-allowed outline-none"
              />
              <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <!-- GitHub -->
            <div>
              <label for="subscriber_github" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                {{ ui_strings.ui_confirm_github_label || "Usuário do GitHub (opcional)" }}
              </label>
              <input
                id="subscriber_github"
                v-model="form.github"
                type="text"
                :placeholder="ui_strings.ui_confirm_github_placeholder || 'ex: octocat'"
                class="w-full bg-surface-dim border border-outline-variant/30 rounded-lg px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-slate-100 placeholder:text-on-surface-variant/40"
              />
              <InputError class="mt-1" :message="form.errors.github" />
            </div>

            <!-- Telefone -->
            <div>
              <label for="subscriber_phone" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                {{ ui_strings.ui_confirm_phone_label || "Telefone (opcional)" }}
              </label>
              <input
                id="subscriber_phone"
                v-model="form.phone"
                type="tel"
                :placeholder="ui_strings.ui_confirm_phone_placeholder || '+55 11 99999-9999'"
                class="w-full bg-surface-dim border border-outline-variant/30 rounded-lg px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-slate-100 placeholder:text-on-surface-variant/40"
              />
              <InputError class="mt-1" :message="form.errors.phone" />
            </div>

            <!-- Idioma -->
            <div>
              <label for="subscriber_lang" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                {{ ui_strings.ui_confirm_lang_label || "Idioma de Preferência" }}
              </label>
              <select
                id="subscriber_lang"
                v-model="form.lang"
                class="w-full bg-surface-dim border border-outline-variant/30 rounded-lg px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-slate-100"
              >
                <option value="pt_br">Português (Brasil)</option>
                <option value="en">English</option>
                <option value="pt_pt">Português (Portugal)</option>
                <option value="es">Español</option>
                <option value="fr">Français</option>
              </select>
              <InputError class="mt-1" :message="form.errors.lang" />
            </div>

            <!-- Botão Submit -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full mt-6 bg-primary text-white font-extrabold py-3.5 rounded-lg text-sm glow-hover transition-all disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
            >
              <span v-if="form.processing" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
              <span>{{ form.processing ? (ui_strings.ui_confirm_submitting || "Confirmando...") : (ui_strings.ui_confirm_submit || "Confirmar Inscrição") }}</span>
            </button>
          </form>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  confirmation: {
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

const form = useForm({
  name: "",
  email: props.confirmation?.email || "",
  github: "",
  phone: "",
  lang: props.current_lang || "pt_br",
});

const submit_confirm = () => {
  if (!props.confirmation?.uuid) {
    return;
  }
  form.post(route("newsletter.confirm.submit", { uuid: props.confirmation.uuid }));
};
</script>

