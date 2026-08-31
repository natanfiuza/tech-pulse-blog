<template>
  <footer class="bg-[#000b2b] w-full py-12 border-t border-slate-800 mt-16">
    <div
      class="flex flex-col md:flex-row justify-between items-center px-4 sm:px-12 gap-8 max-w-screen-2xl mx-auto"
    >
      <div class="flex flex-col items-center md:items-start gap-2">
        <Link
          href="/"
          class="text-lg font-black text-slate-200 uppercase tracking-tighter no-underline"
        >
          TechPulse
        </Link>
        <p class="text-primary text-[10px] uppercase tracking-[0.2em] font-bold">
          O Pulsar da Tecnologia.
        </p>
      </div>
      <nav
        class="flex flex-wrap justify-center gap-8 text-xs uppercase tracking-widest font-bold"
        aria-label="Links do rodapé"
      >
        <Link href="/" class="text-slate-500 hover:text-white transition-colors no-underline">
          Início
        </Link>
        <Link
          href="/#newsletter"
          class="text-slate-500 hover:text-white transition-colors no-underline"
        >
          Newsletter
        </Link>
        <Link
          :href="conta_link"
          class="text-slate-500 hover:text-white transition-colors no-underline"
        >
          {{ conta_label }}
        </Link>
        <a
          href="mailto:contato@natanfiuza.dev.br"
          class="text-slate-500 hover:text-white transition-colors no-underline"
        >
          Contato
        </a>
      </nav>
      <p class="text-slate-600 text-[10px] tracking-widest uppercase">
        © {{ ano }} TechPulse - O Pulsar da Tecnologia.
      </p>
    </div>
  </footer>
</template>

<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const usuario = computed(() => page.props.auth?.user ?? null);
const conta_link = computed(() => (usuario.value ? route("admin.home") : route("login")));
const conta_label = computed(() => (usuario.value ? "Painel" : "Login"));
const ano = new Date().getFullYear();
</script>
