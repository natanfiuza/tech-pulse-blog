<template>
  <div class="min-h-screen bg-background text-on-background font-body flex flex-col">
    <Navbar />

    <main class="flex-grow w-full max-w-md mx-auto px-4 sm:px-8 py-12 flex items-start justify-center">
      <div
        class="w-full bg-surface-container-low rounded-xl border border-outline-variant/20 p-8 shadow-2xl relative overflow-hidden"
      >
        <div
          class="absolute -top-24 -right-24 w-64 h-64 bg-primary/20 rounded-full blur-[100px] pointer-events-none"
        ></div>

        <header class="mb-8 relative">
          <h1 class="font-display font-black text-2xl tracking-tight text-on-surface">
            Criar Conta
          </h1>
          <p class="text-on-surface-variant text-sm mt-1">
            Cadastre-se para comentar e participar do TechPulse
          </p>
        </header>

        <form class="relative space-y-5" @submit.prevent="submit">
          <div>
            <label for="name" class="block font-headline font-bold text-sm text-on-surface mb-2">
              Nome
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              autofocus
              class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
            />
            <p v-if="form.errors.name" class="text-error text-sm mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label for="email" class="block font-headline font-bold text-sm text-on-surface mb-2">
              E-mail
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
            />
            <p v-if="form.errors.email" class="text-error text-sm mt-1">{{ form.errors.email }}</p>
          </div>

          <div>
            <label for="password" class="block font-headline font-bold text-sm text-on-surface mb-2">
              Senha
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
            />
            <p v-if="form.errors.password" class="text-error text-sm mt-1">{{ form.errors.password }}</p>
          </div>

          <div>
            <label
              for="password_confirmation"
              class="block font-headline font-bold text-sm text-on-surface mb-2"
            >
              Confirmar Senha
            </label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full bg-surface-container-highest border border-outline-variant/30 rounded-lg py-2.5 px-3 text-sm text-on-surface focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-on-surface-variant/50"
            />
            <p v-if="form.errors.password_confirmation" class="text-error text-sm mt-1">
              {{ form.errors.password_confirmation }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-primary hover:bg-surface-tint text-on-primary font-medium py-3 px-4 rounded-lg glow-hover transition-all duration-300 disabled:opacity-50"
          >
            {{ form.processing ? "Cadastrando..." : "Criar Conta" }}
          </button>
        </form>

        <div class="mt-8 relative">
          <p class="text-center text-on-surface-variant text-sm mb-4">Ou cadastre-se com:</p>
          <div class="flex justify-center gap-4">
            <a
              href="/login/google"
              class="flex items-center justify-center w-12 h-12 rounded-full bg-surface-container-highest border border-outline-variant/30 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high hover:border-primary/50 transition-all"
              title="Cadastro com Google"
            >
              <img src="/assets/img/social/google_ico.png" alt="Google" class="w-6 h-6" />
            </a>
          </div>
        </div>

        <p class="text-center text-on-surface-variant text-sm mt-8 relative">
          Já tem uma conta?
          <Link href="/login" class="text-primary font-bold hover:text-inverse-primary">Entrar</Link>
        </p>
      </div>
    </main>
  </div>
</template>

<script>
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";

export default {
  components: {
    Navbar,
    Link,
  },
  setup() {
    const form = useForm({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    });

    function submit() {
      form.post("/register");
    }

    return { form, submit };
  },
};
</script>
