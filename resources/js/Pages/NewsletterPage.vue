<template>
  <Head title="Newsletter - TechPulse" />

  <div class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30 flex flex-col">
    <Navbar :categorias="categorias" />

    <main class="flex-1 pt-28 md:pt-36 pb-20 px-4 sm:px-8 max-w-xl mx-auto w-full">
      <div class="bg-surface-container-high border border-outline-variant/30 rounded-2xl p-6 sm:p-10 shadow-2xl space-y-8">
        <!-- Cabeçalho -->
        <div class="text-center space-y-3">
          <div class="w-16 h-16 bg-primary/20 text-primary rounded-2xl flex items-center justify-center mx-auto shadow-inner">
            <span class="material-symbols-outlined text-3xl">mark_email_unread</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
            {{ ui_strings.ui_subscribe_title || "Boletim Informativo" }}
          </h1>
          <p class="text-on-surface-variant text-xs sm:text-sm leading-relaxed max-w-md mx-auto">
            {{ ui_strings.ui_subscribe_desc || "Receba os pulsos de tecnologia e hacks de programação diretamente no seu e-mail, toda segunda-feira." }}
          </p>
        </div>

        <!-- Destaques / Benefícios -->
        <div class="grid grid-cols-1 gap-3 py-2 border-y border-outline-variant/20">
          <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-lg mt-0.5">terminal</span>
            <div>
              <p class="text-xs font-bold text-slate-200">Conteúdo Técnico Semanal</p>
              <p class="text-[11px] text-on-surface-variant leading-relaxed">
                Artigos práticos sobre desenvolvimento, arquitetura de sistemas e novas ferramentas.
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-lg mt-0.5">auto_awesome</span>
            <div>
              <p class="text-xs font-bold text-slate-200">Inovações em IA & Tecnologia</p>
              <p class="text-[11px] text-on-surface-variant leading-relaxed">
                Análises aprofundadas sobre avanços em inteligência artificial e tendências do mercado.
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-lg mt-0.5">verified_user</span>
            <div>
              <p class="text-xs font-bold text-slate-200">Zero Spam & Privacidade Garantida</p>
              <p class="text-[11px] text-on-surface-variant leading-relaxed">
                Seu e-mail está seguro. Desinscreva-se a qualquer momento com apenas um clique.
              </p>
            </div>
          </div>
        </div>

        <!-- Box de Inscrição com a mesma funcionalidade -->
        <div class="bg-surface-dim/70 border border-outline-variant/30 rounded-xl p-5 sm:p-6 shadow-inner">
          <NewsletterBox :current_lang="current_lang" :ui_strings="ui_strings" />
        </div>

        <!-- Voltar para o Início -->
        <div class="text-center pt-2">
          <Link
            href="/"
            class="inline-flex items-center gap-2 text-xs font-semibold text-on-surface-variant hover:text-white transition-colors no-underline"
          >
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>{{ ui_strings.ui_back_to_home || "Voltar para o Início" }}</span>
          </Link>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import NewsletterBox from "@/Components/NewsletterBox.vue";

defineProps({
  categorias: {
    type: Array,
    default: () => [],
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
</script>

