<template>
  <aside
    v-if="deve_exibir"
    class="relative overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 sm:p-8 shadow-xl mt-12 mb-8"
    aria-label="Sobre o autor"
  >
    <div
      class="absolute -top-16 -right-16 w-48 h-48 bg-primary/15 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-6">
      <!-- Foto de Perfil / Avatar -->
      <div class="relative shrink-0">
        <img
          v-if="avatar_url"
          :src="avatar_url"
          :alt="nome_exibicao"
          class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-primary/40 shadow-lg block"
          loading="lazy"
        />
        <div
          v-else
          class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-primary/20 flex items-center justify-center text-primary text-2xl font-black"
        >
          {{ nome_exibicao.charAt(0) }}
        </div>
      </div>

      <!-- Detalhes do Autor -->
      <div class="flex-1 min-w-0 space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div>
            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-primary block">
              Sobre o Autor
            </span>
            <h3 class="text-xl font-bold font-display text-on-surface">
              {{ nome_exibicao }}
            </h3>
          </div>

          <!-- Link para o perfil público (se habilitado) -->
          <Link
            v-if="perfil_publico_ativo"
            :href="url_perfil_publico"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary-variant hover:underline transition-all"
          >
            <span>Ver perfil completo</span>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </Link>
        </div>

        <!-- Biografia -->
        <p v-if="bio" class="text-sm text-on-surface-variant leading-relaxed line-clamp-3">
          {{ bio }}
        </p>
        <p v-else class="text-xs text-on-surface-variant italic">
          Autor no TechPulse Blog.
        </p>

        <!-- Redes Sociais do Autor -->
        <div v-if="redes_autorizadas.length > 0" class="flex flex-wrap items-center gap-2 pt-1">
          <a
            v-for="rede in redes_autorizadas"
            :key="rede.nome"
            :href="rede.url"
            target="_blank"
            rel="noopener noreferrer"
            :title="rede.rotulo"
            class="inline-flex items-center gap-1 rounded-md border border-outline-variant/30 bg-surface-container px-2.5 py-1 text-xs font-medium text-on-surface hover:border-primary/50 hover:text-primary transition-all"
          >
            <span class="material-symbols-outlined text-xs text-primary">{{ rede.icone }}</span>
            <span>{{ rede.rotulo }}</span>
          </a>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
});

const profile = computed(() => props.user?.profile ?? null);

const deve_exibir = computed(() => {
  if (!props.user) return false;
  // Se o autor configurou para não exibir caixa de autor, oculta
  return profile.value ? profile.value.show_author_box !== false : true;
});

const nome_exibicao = computed(() => {
  if (!props.user) return "Usuário removido";
  if (profile.value && profile.value.show_name === false && profile.value.username) {
    return profile.value.username;
  }
  return props.user.name || "Autor";
});

const avatar_url = computed(() => {
  return props.user?.avatar_url || props.user?.avatar || "";
});

const bio = computed(() => {
  return profile.value?.bio || "";
});

const perfil_publico_ativo = computed(() => {
  return profile.value?.public_profile_enabled !== false && Boolean(profile.value?.username);
});

const url_perfil_publico = computed(() => {
  return `/${profile.value?.username}`;
});

const mapa_icones = {
  github: "code",
  twitter_x: "tag",
  linkedin: "work",
  website: "language",
  instagram: "photo_camera",
};

const mapa_rotulos = {
  github: "GitHub",
  twitter_x: "X",
  linkedin: "LinkedIn",
  website: "Website",
  instagram: "Instagram",
};

const redes_autorizadas = computed(() => {
  const links = profile.value?.social_links;
  if (!links) return [];

  const resultado = [];
  for (const [chave, config] of Object.entries(links)) {
    if (config?.url && config?.show !== false) {
      resultado.push({
        nome: chave,
        rotulo: mapa_rotulos[chave] || chave,
        icone: mapa_icones[chave] || "link",
        url: config.url,
      });
    }
  }
  return resultado;
});
</script>

