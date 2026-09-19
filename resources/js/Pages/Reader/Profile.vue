<template>
  <div class="min-h-screen bg-background text-on-background font-body flex flex-col">
    <Head title="Meu Perfil - TechPulse" />

    <Navbar />

    <main class="flex-grow w-full max-w-5xl mx-auto px-4 sm:px-8 py-28 pb-20 space-y-6">
      <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <Link
              href="/minha-conta"
              class="text-xs text-primary hover:underline flex items-center gap-1 font-semibold"
            >
              <span class="material-symbols-outlined text-sm">arrow_back</span>
              Voltar para Minha Conta
            </Link>
          </div>
          <h1 class="font-display font-black text-3xl tracking-tight text-on-surface">Configurações de Perfil</h1>
          <p class="text-sm text-on-surface-variant mt-1">
            Gerencie sua foto, biografia, redes sociais e privacidade
          </p>
        </div>

        <a
          v-if="profile_user.public_profile_enabled"
          :href="profile_user.public_url"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 rounded-lg border border-outline-variant/30 bg-surface-container-high px-4 py-2 text-xs font-semibold text-on-surface hover:border-primary/50 hover:text-primary transition-all self-start sm:self-auto"
        >
          <span class="material-symbols-outlined text-sm">open_in_new</span>
          <span>Ver perfil público</span>
        </a>
      </header>

      <ProfileMasterDetails
        :profile_user="profile_user"
        submit_url="/minha-conta/perfil"
      />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import ProfileMasterDetails from "@/Components/Profile/ProfileMasterDetails.vue";

defineProps({
  profile_user: {
    type: Object,
    required: true,
  },
});
</script>

