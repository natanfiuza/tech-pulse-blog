<template>
  <div class="min-h-screen bg-background text-on-background font-body">
    <Navbar />

    <main class="pt-24 md:pt-28 pb-20 px-4 sm:px-8 max-w-3xl mx-auto">

      <!-- Cabeçalho da página -->
      <header class="mb-10">
        <div class="flex items-center gap-3 mb-4">
          <span class="material-symbols-outlined text-4xl text-primary">edit_note</span>
          <h1 class="font-display font-black text-3xl md:text-4xl tracking-tight text-on-surface">
            Torne-se um Autor no TechPulse
          </h1>
        </div>
        <p class="text-on-surface-variant text-base leading-relaxed">
          Compartilhe seu conhecimento com desenvolvedores de todo o Brasil.
        </p>
      </header>

      <!-- Flash messages -->
      <div
        v-if="flash_success"
        class="mb-8 flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-600 dark:text-emerald-400"
        role="status"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">check_circle</span>
        {{ flash_success }}
      </div>
      <div
        v-if="flash_error"
        class="mb-8 flex items-center gap-2 rounded-lg border border-error/30 bg-error/10 px-4 py-3 text-sm text-error"
        role="alert"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">error</span>
        {{ flash_error }}
      </div>

      <!-- Bloco de informações: o que é ser autor -->
      <section class="mb-10 rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 md:p-8 shadow-sm space-y-6">
        <div>
          <h2 class="font-headline font-bold text-lg text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl" aria-hidden="true">info</span>
            O que é ser um Autor?
          </h2>
          <p class="text-sm text-on-surface-variant leading-relaxed">
            Autores têm acesso ao painel administrativo do TechPulse, onde podem criar, editar e
            gerenciar seus próprios posts. Todo o conteúdo é escrito em Markdown com suporte a
            imagens, código com destaque de sintaxe, diagramas Mermaid e muito mais.
          </p>
        </div>

        <div>
          <h2 class="font-headline font-bold text-lg text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl" aria-hidden="true">rule</span>
            Regras de Publicação
          </h2>
          <ul class="space-y-2 text-sm text-on-surface-variant">
            <li class="flex items-start gap-2">
              <span class="material-symbols-outlined text-primary text-base mt-0.5" aria-hidden="true">check_circle</span>
              Conteúdo original, técnico e relevante para a comunidade de desenvolvedores.
            </li>
            <li class="flex items-start gap-2">
              <span class="material-symbols-outlined text-primary text-base mt-0.5" aria-hidden="true">check_circle</span>
              Português (pt-BR) como idioma principal dos posts.
            </li>
            <li class="flex items-start gap-2">
              <span class="material-symbols-outlined text-primary text-base mt-0.5" aria-hidden="true">check_circle</span>
              Proibido conteúdo promocional, spam ou plágio. Posts assim serão removidos sem aviso.
            </li>
            <li class="flex items-start gap-2">
              <span class="material-symbols-outlined text-primary text-base mt-0.5" aria-hidden="true">check_circle</span>
              Respeite licenças de código e cite suas fontes adequadamente.
            </li>
          </ul>
        </div>

        <div class="rounded-lg border border-primary/20 bg-primary/5 p-4">
          <h2 class="font-headline font-bold text-sm text-on-surface mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base" aria-hidden="true">manage_accounts</span>
            Moderação de Publicações
          </h2>
          <p class="text-sm text-on-surface-variant leading-relaxed">
            O TechPulse adota um modelo de publicação com moderação pelo administrador.
            Após seu cadastro como autor, você poderá criar e salvar posts normalmente.
            A aprovação e publicação ficam sujeitas à revisão da equipe editorial, garantindo
            a qualidade e a confiabilidade do conteúdo publicado no blog.
          </p>
        </div>
      </section>

      <!-- ───────── ESTADO: já é admin ───────── -->
      <section v-if="ja_e_admin" class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-6 text-center space-y-3">
        <span class="material-symbols-outlined text-4xl text-amber-500" aria-hidden="true">admin_panel_settings</span>
        <h2 class="font-headline font-bold text-lg text-on-surface">Você é Administrador</h2>
        <p class="text-sm text-on-surface-variant">
          Como admin você já possui acesso total ao TechPulse, incluindo a publicação de conteúdo.
        </p>
        <Link
          href="/admin/home"
          class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-on-primary glow-hover transition-all"
        >
          <span class="material-symbols-outlined text-base">dashboard</span>
          Ir ao Painel Admin
        </Link>
      </section>

      <!-- ───────── ESTADO: já é autor ───────── -->
      <section v-else-if="ja_e_autor" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-6 text-center space-y-3">
        <span class="material-symbols-outlined text-4xl text-emerald-500" aria-hidden="true">verified</span>
        <h2 class="font-headline font-bold text-lg text-on-surface">Você já é Autor no TechPulse!</h2>
        <p class="text-sm text-on-surface-variant">
          Seu acesso ao painel de criação de posts já está ativo. Boas publicações!
        </p>
        <Link
          href="/admin/home"
          class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-on-primary glow-hover transition-all"
        >
          <span class="material-symbols-outlined text-base">edit_note</span>
          Ir ao Painel Admin
        </Link>
      </section>

      <!-- ───────── ESTADO: solicitação pendente ───────── -->
      <section v-else-if="solicitacao_pendente" class="rounded-xl border border-primary/30 bg-primary/5 p-6 text-center space-y-3">
        <span class="material-symbols-outlined text-4xl text-primary" aria-hidden="true">hourglass_top</span>
        <h2 class="font-headline font-bold text-lg text-on-surface">Solicitação em Análise</h2>
        <p class="text-sm text-on-surface-variant">
          Recebemos sua solicitação em <strong>{{ solicitacao.created_at }}</strong>.
          Nossa equipe revisará em breve e entrará em contato.
        </p>
        <p class="text-xs text-on-surface-variant/70 italic">
          "{{ solicitacao.motivacao?.slice(0, 120) }}{{ (solicitacao.motivacao?.length ?? 0) > 120 ? '…' : '' }}"
        </p>
      </section>

      <!-- ───────── ESTADO: solicitação rejeitada ───────── -->
      <section v-else-if="solicitacao_rejeitada" class="rounded-xl border border-error/30 bg-error/5 p-6 space-y-4">
        <div class="flex items-center gap-3">
          <span class="material-symbols-outlined text-3xl text-error" aria-hidden="true">cancel</span>
          <div>
            <h2 class="font-headline font-bold text-lg text-on-surface">Solicitação Não Aprovada</h2>
            <p class="text-sm text-on-surface-variant">Sua solicitação anterior foi revisada e não foi aprovada desta vez.</p>
          </div>
        </div>
        <div v-if="solicitacao.admin_nota" class="rounded-lg bg-surface-container-high p-4 text-sm text-on-surface-variant">
          <strong class="block mb-1 text-on-surface">Nota da equipe:</strong>
          {{ solicitacao.admin_nota }}
        </div>
        <p class="text-sm text-on-surface-variant">Você pode fazer uma nova solicitação abaixo.</p>
        <!-- cai no formulário abaixo -->
        <FormularioSolicitacao @submitted="submit_solicitacao" :processando="form.processing" :erros="form.errors" v-model:motivacao="form.motivacao" />
      </section>

      <!-- ───────── ESTADO: leitor logado → formulário de solicitação ───────── -->
      <section v-else-if="usuario_logado_leitor">
        <h2 class="font-headline font-bold text-xl text-on-surface mb-6">
          Solicitar perfil de Autor
        </h2>
        <FormularioSolicitacao @submitted="submit_solicitacao" :processando="form.processing" :erros="form.errors" v-model:motivacao="form.motivacao" />
      </section>

      <!-- ───────── ESTADO: não logado → cadastro + depois solicitação ───────── -->
      <section v-else>
        <!-- Etapa 1: cadastro (sem Google) -->
        <div v-if="!cadastro_concluido">
          <h2 class="font-headline font-bold text-xl text-on-surface mb-2">
            1. Crie sua conta
          </h2>
          <p class="text-sm text-on-surface-variant mb-6">
            Após criar sua conta como leitor, você poderá enviar a solicitação para se tornar autor.
          </p>

          <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-6 md:p-8 shadow-sm">
            <form class="space-y-5" @submit.prevent="submit_cadastro">
              <div>
                <label for="reg_name" class="block font-headline font-bold text-sm text-on-surface mb-2">Nome</label>
                <input
                  id="reg_name"
                  v-model="form_registro.name"
                  type="text"
                  required
                  autofocus
                  class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
                />
                <p v-if="form_registro.errors.name" class="text-error text-sm mt-1">{{ form_registro.errors.name }}</p>
              </div>

              <div>
                <label for="reg_email" class="block font-headline font-bold text-sm text-on-surface mb-2">E-mail</label>
                <input
                  id="reg_email"
                  v-model="form_registro.email"
                  type="email"
                  required
                  class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
                />
                <p v-if="form_registro.errors.email" class="text-error text-sm mt-1">{{ form_registro.errors.email }}</p>
              </div>

              <div>
                <label for="reg_password" class="block font-headline font-bold text-sm text-on-surface mb-2">Senha</label>
                <input
                  id="reg_password"
                  v-model="form_registro.password"
                  type="password"
                  required
                  class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
                />
                <p v-if="form_registro.errors.password" class="text-error text-sm mt-1">{{ form_registro.errors.password }}</p>
              </div>

              <div>
                <label for="reg_password_confirmation" class="block font-headline font-bold text-sm text-on-surface mb-2">Confirmar Senha</label>
                <input
                  id="reg_password_confirmation"
                  v-model="form_registro.password_confirmation"
                  type="password"
                  required
                  class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
                />
                <p v-if="form_registro.errors.password_confirmation" class="text-error text-sm mt-1">{{ form_registro.errors.password_confirmation }}</p>
              </div>

              <button
                type="submit"
                :disabled="form_registro.processing"
                class="w-full bg-primary hover:bg-surface-tint text-on-primary font-medium py-3 px-4 rounded-lg glow-hover transition-all duration-300 disabled:opacity-50"
              >
                {{ form_registro.processing ? 'Criando conta...' : 'Criar Conta e Continuar' }}
              </button>
            </form>

            <p class="text-center text-on-surface-variant text-sm mt-6">
              Já tem uma conta?
              <Link href="/login" class="text-primary font-bold hover:text-inverse-primary">Entrar</Link>
            </p>
          </div>
        </div>

        <!-- Etapa 2: nunca chega aqui via client-side, o redirect do servidor trata -->
      </section>

    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import FormularioSolicitacao from "@/Components/FormularioSolicitacao.vue";

const props = defineProps({
  solicitacao: {
    type: Object,
    default: null,
  },
});

const page = usePage();

// ─── estado do usuário ───────────────────────────────────────────────────────
const auth_user = computed(() => page.props.auth?.user ?? null);
const usuario_logado = computed(() => !!auth_user.value);
const ja_e_autor = computed(() => auth_user.value?.role === "autor");
const ja_e_admin = computed(() => auth_user.value?.role === "admin");
const usuario_logado_leitor = computed(
  () => usuario_logado.value && auth_user.value?.role === "leitor"
);

// ─── estado da solicitação ───────────────────────────────────────────────────
const solicitacao_pendente = computed(
  () => props.solicitacao?.status === "pendente"
);
const solicitacao_rejeitada = computed(
  () => props.solicitacao?.status === "rejeitada"
);

// ─── flash ───────────────────────────────────────────────────────────────────
const flash_success = computed(() => page.props.flash?.success ?? null);
const flash_error   = computed(() => page.props.flash?.error   ?? null);

// ─── formulário de solicitação de autor ──────────────────────────────────────
const form = useForm({ motivacao: "" });

const submit_solicitacao = () => {
  form.post("/tornar-se-autor", { preserveScroll: true });
};

// ─── formulário de cadastro (não logado) ─────────────────────────────────────
const cadastro_concluido = ref(false);

const form_registro = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const submit_cadastro = () => {
  form_registro.post("/register?next=/tornar-se-autor");
};
</script>

