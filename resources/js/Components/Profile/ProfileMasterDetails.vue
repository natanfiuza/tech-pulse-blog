<template>
  <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low shadow-2xl overflow-hidden">
    <!-- Feedback de Sucesso -->
    <div
      v-if="$page.props.flash?.success"
      class="bg-emerald-500/10 border-b border-emerald-500/20 text-emerald-400 px-6 py-4 flex items-center gap-3 text-sm"
    >
      <span class="material-symbols-outlined text-lg">check_circle</span>
      <span>{{ $page.props.flash.success }}</span>
    </div>

    <!-- Feedback de Erro Geral -->
    <div
      v-if="tem_erros"
      class="bg-error/10 border-b border-error/20 text-error px-6 py-4 flex items-center gap-3 text-sm"
    >
      <span class="material-symbols-outlined text-lg">error</span>
      <span>Verifique os campos destacados abaixo antes de salvar.</span>
    </div>

    <form @submit.prevent="salvar_perfil">
      <div class="grid grid-cols-1 md:grid-cols-12 min-h-[540px]">
        <!-- MASTER (Menu lateral de seções) -->
        <aside class="md:col-span-4 border-b md:border-b-0 md:border-r border-outline-variant/20 bg-surface-container/50 p-4 sm:p-6 space-y-2">
          <h3 class="text-xs font-bold uppercase tracking-wider text-on-surface-variant px-3 mb-4 font-mono">
            Configurações
          </h3>

          <button
            v-for="aba in abas"
            :key="aba.id"
            type="button"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-left text-sm font-medium transition-all duration-200"
            :class="
              aba_ativa === aba.id
                ? 'bg-primary text-on-primary font-bold shadow-md'
                : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'
            "
            @click="selecionar_aba(aba.id)"
          >
            <span class="material-symbols-outlined text-xl">{{ aba.icone }}</span>
            <div class="flex flex-col">
              <span>{{ aba.titulo }}</span>
              <span
                class="text-[11px] opacity-75 font-normal"
                :class="aba_ativa === aba.id ? 'text-on-primary/90' : 'text-on-surface-variant'"
              >
                {{ aba.descricao }}
              </span>
            </div>
          </button>
        </aside>

        <!-- DETAILS (Conteúdo da seção ativa) -->
        <div class="md:col-span-8 p-6 sm:p-8">
          <!-- ABA 1: IDENTIDADE & FOTO -->
          <div v-show="aba_ativa === 'identidade'" class="space-y-6">
            <div>
              <h2 class="text-lg font-bold text-on-surface">Identidade &amp; Foto</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Gerencie sua foto de perfil, dados de exibição e biografia pública.
              </p>
            </div>

            <!-- Foto de Perfil & Avatar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-4 rounded-xl border border-outline-variant/20 bg-surface-container-high/40">
              <div class="relative group">
                <img
                  :src="preview_avatar || form.avatar_url"
                  :alt="form.name"
                  class="w-20 h-20 rounded-full object-cover border-2 border-primary/50 shadow-md block"
                />
              </div>

              <div class="space-y-2 flex-1">
                <div class="flex flex-wrap gap-3">
                  <label
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-surface-container-highest px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-tint/20 hover:text-primary transition-colors border border-outline-variant/30"
                  >
                    <span class="material-symbols-outlined text-sm">photo_camera</span>
                    <span>Alterar foto</span>
                    <input
                      type="file"
                      class="hidden"
                      accept="image/png,image/jpeg,image/webp"
                      @change="selecionar_foto"
                    />
                  </label>

                  <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-error/10 px-4 py-2 text-xs font-semibold text-error hover:bg-error/20 transition-colors border border-error/20"
                    @click="redefinir_foto_padrao"
                  >
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    <span>Restaurar iniciais padrão</span>
                  </button>
                </div>
                <p class="text-[11px] text-on-surface-variant">
                  Formatos aceitos: PNG, JPG ou WebP (máximo 5MB). O avatar padrão exibe suas iniciais em fundo pastel.
                </p>
                <p v-if="form.errors.avatar" class="text-xs text-error mt-1">
                  {{ form.errors.avatar }}
                </p>
              </div>
            </div>

            <!-- Nome Completo -->
            <div>
              <label for="campo_nome" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                Nome Completo <span class="text-error">*</span>
              </label>
              <input
                id="campo_nome"
                v-model="form.name"
                type="text"
                required
                class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-4 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                placeholder="Seu nome completo"
              />
              <p v-if="form.errors.name" class="text-xs text-error mt-1">
                {{ form.errors.name }}
              </p>
            </div>

            <!-- Nome de Usuário (URL Pública) -->
            <div>
              <label for="campo_username" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-2">
                Nome de Usuário (URL do Perfil) <span class="text-error">*</span>
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-on-surface-variant font-mono">
                  /
                </span>
                <input
                  id="campo_username"
                  v-model="form.username"
                  type="text"
                  required
                  class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-high pl-7 pr-4 py-2.5 text-sm font-mono text-on-surface placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  placeholder="natanfiuza"
                  @input="higienizar_username"
                />
              </div>
              <p class="text-[11px] text-on-surface-variant mt-1.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">link</span>
                Seu perfil público será:
                <span class="text-primary font-mono font-medium truncate">
                  {{ url_base }}/{{ form.username }}
                </span>
              </p>
              <p v-if="form.errors.username" class="text-xs text-error mt-1">
                {{ form.errors.username }}
              </p>
            </div>

            <!-- Biografia -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <label for="campo_bio" class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider">
                  Biografia do Autor
                </label>
                <span class="text-[11px] text-on-surface-variant font-mono">
                  {{ form.bio ? form.bio.length : 0 }}/1000
                </span>
              </div>
              <textarea
                id="campo_bio"
                v-model="form.bio"
                rows="4"
                maxlength="1000"
                class="w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-4 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant/50 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary leading-relaxed"
                placeholder="Escreva uma breve apresentação sobre sua carreira, interesses de tecnologia e projetos..."
              ></textarea>
              <p v-if="form.errors.bio" class="text-xs text-error mt-1">
                {{ form.errors.bio }}
              </p>
            </div>
          </div>

          <!-- ABA 2: REDES SOCIAIS -->
          <div v-show="aba_ativa === 'redes'" class="space-y-6">
            <div>
              <h2 class="text-lg font-bold text-on-surface">Redes Sociais &amp; Links</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Informe os links dos seus perfis e selecione quais devem ser exibidos publicamente.
              </p>
            </div>

            <div class="space-y-4">
              <!-- Item de Rede Social -->
              <div
                v-for="rede in redes_disponiveis"
                :key="rede.chave"
                class="p-4 rounded-xl border border-outline-variant/20 bg-surface-container-high/40 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">{{ rede.icone }}</span>
                    <span class="text-sm font-bold text-on-surface">{{ rede.nome }}</span>
                  </div>
                  <label class="flex items-center gap-2 cursor-pointer text-xs text-on-surface-variant select-none">
                    <input
                      v-model="form.social_links[rede.chave].show"
                      type="checkbox"
                      class="rounded border-outline-variant/40 text-primary focus:ring-primary"
                    />
                    <span>Exibir no perfil</span>
                  </label>
                </div>

                <div class="relative">
                  <input
                    v-model="form.social_links[rede.chave].url"
                    type="url"
                    :placeholder="rede.placeholder"
                    class="w-full rounded-lg border border-outline-variant/30 bg-surface-container px-3.5 py-2 text-sm text-on-surface placeholder:text-on-surface-variant/40 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- ABA 3: PRIVACIDADE & EXIBIÇÃO -->
          <div v-show="aba_ativa === 'privacidade'" class="space-y-6">
            <div>
              <h2 class="text-lg font-bold text-on-surface">Privacidade &amp; Visibilidade</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Defina o que fica visível ao público no seu perfil e nas páginas dos seus artigos.
              </p>
            </div>

            <div class="divide-y divide-outline-variant/20 rounded-xl border border-outline-variant/20 bg-surface-container-high/40">
              <!-- Toggle 1: Perfil Público Geral -->
              <div class="p-4 sm:p-5 flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <span class="text-sm font-bold text-on-surface block">
                    Habilitar Perfil Público
                  </span>
                  <p class="text-xs text-on-surface-variant leading-relaxed">
                    Permite que qualquer pessoa acesse sua página de perfil em
                    <span class="font-mono text-primary">/{{ form.username }}</span> com a listagem dos seus posts.
                  </p>
                </div>
                <input
                  v-model="form.public_profile_enabled"
                  type="checkbox"
                  class="h-5 w-5 rounded border-outline-variant/40 text-primary focus:ring-primary cursor-pointer shrink-0 mt-0.5"
                />
              </div>

              <!-- Toggle 2: Exibir Nome Completo -->
              <div class="p-4 sm:p-5 flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <span class="text-sm font-bold text-on-surface block">
                    Exibir Nome Completo
                  </span>
                  <p class="text-xs text-on-surface-variant leading-relaxed">
                    Mostra seu nome real nos artigos e perfil. Quando desativado, apenas seu nome de usuário será exibido.
                  </p>
                </div>
                <input
                  v-model="form.show_name"
                  type="checkbox"
                  class="h-5 w-5 rounded border-outline-variant/40 text-primary focus:ring-primary cursor-pointer shrink-0 mt-0.5"
                />
              </div>

              <!-- Toggle 3: Exibir E-mail -->
              <div class="p-4 sm:p-5 flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <span class="text-sm font-bold text-on-surface block">
                    Exibir E-mail no Perfil
                  </span>
                  <p class="text-xs text-on-surface-variant leading-relaxed">
                    Torna seu endereço de e-mail visível publicamente para contato de leitores.
                  </p>
                </div>
                <input
                  v-model="form.show_email"
                  type="checkbox"
                  class="h-5 w-5 rounded border-outline-variant/40 text-primary focus:ring-primary cursor-pointer shrink-0 mt-0.5"
                />
              </div>

              <!-- Toggle 4: Caixa de Autor nos Posts -->
              <div class="p-4 sm:p-5 flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <span class="text-sm font-bold text-on-surface block">
                    Exibir Caixa de Autor no Rodapé dos Posts
                  </span>
                  <p class="text-xs text-on-surface-variant leading-relaxed">
                    Inclui sua foto, biografia e links de redes sociais ao final de cada artigo publicado por você.
                  </p>
                </div>
                <input
                  v-model="form.show_author_box"
                  type="checkbox"
                  class="h-5 w-5 rounded border-outline-variant/40 text-primary focus:ring-primary cursor-pointer shrink-0 mt-0.5"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BARRA DE AÇÃO INFERIOR COM BOTÃO VERDE DE SALVAMENTO -->
      <footer class="border-t border-outline-variant/20 bg-surface-container px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs text-on-surface-variant flex items-center gap-1.5">
          <span class="material-symbols-outlined text-sm">info</span>
          <span>Todas as alterações passam a valer imediatamente no blog.</span>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg transition-all duration-200 hover:bg-emerald-500 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/60"
        >
          <span
            v-if="form.processing"
            class="material-symbols-outlined animate-spin text-lg"
          >
            progress_activity
          </span>
          <span v-else class="material-symbols-outlined text-lg">check</span>
          <span>{{ form.processing ? "Salvando..." : "Salvar Alterações" }}</span>
        </button>
      </footer>
    </form>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  profile_user: {
    type: Object,
    required: true,
  },
  submit_url: {
    type: String,
    required: true,
  },
});

const aba_ativa = ref("identidade");
const preview_avatar = ref(null);

const abas = [
  {
    id: "identidade",
    titulo: "Identidade & Foto",
    icone: "account_circle",
    descricao: "Foto, nome, username e bio",
  },
  {
    id: "redes",
    titulo: "Redes Sociais",
    icone: "share",
    descricao: "GitHub, LinkedIn, X e links",
  },
  {
    id: "privacidade",
    titulo: "Privacidade",
    icone: "security",
    descricao: "Visibilidade e regras públicas",
  },
];

const redes_disponiveis = [
  {
    chave: "github",
    nome: "GitHub",
    icone: "code",
    placeholder: "https://github.com/seunome",
  },
  {
    chave: "twitter_x",
    nome: "X (Twitter)",
    icone: "tag",
    placeholder: "https://x.com/seunome",
  },
  {
    chave: "linkedin",
    nome: "LinkedIn",
    icone: "work",
    placeholder: "https://linkedin.com/in/seunome",
  },
  {
    chave: "website",
    nome: "Website Pessoal",
    icone: "language",
    placeholder: "https://seusite.dev.br",
  },
  {
    chave: "instagram",
    nome: "Instagram",
    icone: "photo_camera",
    placeholder: "https://instagram.com/seunome",
  },
];

// Inicialização segura dos social_links com estrutura padronizada
const inicializar_social_links = () => {
  const base = {};
  redes_disponiveis.forEach((item) => {
    const existente = props.profile_user.social_links?.[item.chave];
    base[item.chave] = {
      url: existente?.url || "",
      show: existente?.show !== false,
    };
  });
  return base;
};

const form = useForm({
  _method: "PUT",
  name: props.profile_user.name || "",
  username: props.profile_user.username || "",
  bio: props.profile_user.bio || "",
  avatar: null,
  avatar_url: props.profile_user.avatar_url || "",
  reset_avatar: false,
  social_links: inicializar_social_links(),
  public_profile_enabled: props.profile_user.public_profile_enabled ?? true,
  show_email: props.profile_user.show_email ?? false,
  show_name: props.profile_user.show_name ?? true,
  show_author_box: props.profile_user.show_author_box ?? true,
});

const url_base = typeof window !== "undefined" ? window.location.origin : "";

const tem_erros = computed(() => {
  return Object.keys(form.errors).length > 0;
});

const selecionar_aba = (id) => {
  aba_ativa.value = id;
};

const higienizar_username = () => {
  form.username = form.username
    .toLowerCase()
    .replace(/[^a-z0-9_\-\.]/g, "");
};

const selecionar_foto = (evento) => {
  const arquivo = evento.target.files[0];
  if (!arquivo) return;

  form.avatar = arquivo;
  form.reset_avatar = false;

  const reader = new FileReader();
  reader.onload = (e) => {
    preview_avatar.value = e.target.result;
  };
  reader.readAsDataURL(arquivo);
};

const redefinir_foto_padrao = () => {
  form.avatar = null;
  form.reset_avatar = true;
  // Pré-visualização simples do fallback
  preview_avatar.value = form.avatar_url;
};

const salvar_perfil = () => {
  form.post(props.submit_url, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      form.avatar = null;
      form.reset_avatar = false;
      preview_avatar.value = null;
    },
  });
};
</script>

