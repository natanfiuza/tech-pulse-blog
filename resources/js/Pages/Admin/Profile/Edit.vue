<template>
  <AdminLayout>
    <Head title="Meu Perfil - Admin TechPulse" />

    <div class="max-w-5xl mx-auto space-y-6">
      <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold font-headline text-on-surface">Meu Perfil</h1>
          <p class="text-sm text-on-surface-variant mt-1">
            Gerencie sua foto, dados públicos de autor, redes sociais e privacidade
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
          <span>Ver meu perfil público</span>
        </a>
      </header>

      <ProfileMasterDetails
        :profile_user="profile_user"
        submit_url="/admin/perfil"
      />
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ProfileMasterDetails from "@/Components/Profile/ProfileMasterDetails.vue";

defineProps({
  profile_user: {
    type: Object,
    required: true,
  },
});
</script>

