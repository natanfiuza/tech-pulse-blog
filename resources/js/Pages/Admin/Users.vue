<template>
  <AdminLayout>
    <div class="mx-auto max-w-7xl">
      <!-- Cabeçalho -->
      <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="font-headline text-2xl font-extrabold text-on-surface md:text-3xl">Usuários</h1>
          <p class="mt-1 text-sm text-on-surface-variant">
            {{ usuarios.length }} usuário(s) · Somente administradores definem privilégios
          </p>
        </div>
      </div>

      <!-- Mensagens flash -->
      <div
        v-if="success_message"
        class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400"
        role="status"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">check_circle</span>
        {{ success_message }}
      </div>
      <div
        v-if="error_message"
        class="mb-6 flex items-center gap-2 rounded-lg border border-error/30 bg-error/10 px-4 py-3 text-sm text-error"
        role="alert"
      >
        <span class="material-symbols-outlined text-base" aria-hidden="true">error</span>
        {{ error_message }}
      </div>

      <!-- Lista vazia -->
      <div
        v-if="usuarios.length === 0"
        class="rounded-xl border border-dashed border-outline-variant/30 bg-surface-container-low p-12 text-center text-on-surface-variant"
      >
        Nenhum usuário encontrado.
      </div>

      <!-- Lista de usuários -->
      <div class="space-y-4">
        <article
          v-for="usuario in usuarios"
          :key="usuario.id"
          class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-2xl transition-colors hover:border-primary/30"
        >
          <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <div class="flex min-w-0 flex-1 items-center gap-4">
              <img
                v-if="usuario.avatar"
                :src="usuario.avatar"
                :alt="`Foto de ${usuario.name}`"
                class="h-12 w-12 rounded-full object-cover border border-outline-variant/30"
              />
              <div
                v-else
                class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/20 font-bold text-primary"
              >
                {{ inicial(usuario) }}
              </div>
              <div class="min-w-0">
                <h2 class="font-headline text-lg font-bold text-on-surface truncate">
                  {{ usuario.name }}
                  <span v-if="eh_eu(usuario)" class="text-xs font-normal text-on-surface-variant">
                    (você)
                  </span>
                </h2>
                <p class="truncate text-sm text-on-surface-variant">{{ usuario.email }}</p>
                <p class="mt-1 font-mono text-xs text-on-surface-variant">
                  {{ usuario.posts_count }} post(s)
                </p>
              </div>
            </div>

            <div class="flex shrink-0 items-center gap-2 md:flex-col md:items-end">
              <span
                :class="papel_chip(usuario.role)"
                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide uppercase"
              >
                {{ rotulo_papel(usuario.role) }}
              </span>
              <div v-if="pode_gerenciar(usuario)" class="flex items-center gap-2">
                <select
                  :value="usuario.role"
                  :aria-label="`Alterar perfil de ${usuario.name}`"
                  class="rounded-lg border border-outline-variant/20 bg-surface px-3 py-2 text-sm text-on-surface outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary"
                  @change="alterar_papel(usuario, $event.target.value)"
                >
                  <option value="leitor">Leitor</option>
                  <option value="autor">Autor</option>
                  <option value="admin">Admin</option>
                </select>
                <button
                  type="button"
                  class="inline-flex items-center gap-1.5 rounded-lg border border-error/30 bg-error/10 px-3 py-2 text-sm font-medium text-error transition-colors hover:bg-error/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-error/60"
                  @click="excluir(usuario)"
                >
                  <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                  Excluir
                </button>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const rotulos_papel = {
    leitor: "Leitor",
    autor: "Autor",
    admin: "Admin",
};

const classes_papel = {
    leitor: "bg-surface-container-highest text-on-surface-variant",
    autor: "bg-primary/20 text-primary",
    admin: "bg-amber-500/20 text-amber-400",
};

export default {
    components: {
        AdminLayout,
    },
    props: {
        usuarios: { type: Array, default: () => [] },
    },
    setup() {
        const page = usePage();
        const form_papel = useForm({});
        const form_excluir = useForm({});

        const eh_eu = (usuario) => usuario.id === page.props.auth?.user?.id;
        // Admin não gerencia a si mesmo nem outros admins (evita lockout)
        const pode_gerenciar = (usuario) => !eh_eu(usuario) && usuario.role !== "admin";

        function inicial(usuario) {
            return (usuario.name || "?").charAt(0).toUpperCase();
        }

        function alterar_papel(usuario, papel) {
            form_papel.put(route("users.update_role", { user: usuario.id }), { role: papel });
        }

        function excluir(usuario) {
            if (
                !window.confirm(
                    `Remover o usuário "${usuario.name}"? Ele deixará de acessar o blog (conteúdo e comentários são preservados).`
                )
            ) {
                return;
            }
            form_excluir.delete(route("users.destroy", { user: usuario.id }));
        }

        return { eh_eu, pode_gerenciar, inicial, alterar_papel, excluir };
    },
    computed: {
        success_message() {
            return this.$page.props.flash?.success;
        },
        error_message() {
            return this.$page.props.flash?.error;
        },
    },
    methods: {
        rotulo_papel(papel) {
            return rotulos_papel[papel] ?? papel;
        },
        papel_chip(papel) {
            return classes_papel[papel] ?? classes_papel.leitor;
        },
    },
};
</script>
