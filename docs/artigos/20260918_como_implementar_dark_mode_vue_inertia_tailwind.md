# Como Implementar Dark Mode sem FOUC com Vue 3, Inertia.js e Tailwind CSS

A alternância entre tema claro e tema escuro (**Dark Mode / Light Mode**) tornou-se um padrão essencial de experiência do usuário (UX) em aplicações web modernas. No entanto, desenvolvedores frequentemente enfrentam desafios como o temido **FOUC (Flash of Unstyled Content)** — quando a página pisca com o tema incorreto antes de carregar o JavaScript — e a sincronização reativa de estado entre múltiplos componentes.

Neste artigo, vamos explorar a arquitetura completa adotada no **TechPulse Blog** para implementar uma troca de temas instantânea, fluida, persistente e acessível.

---

## 1. O Problema do Flash de Carregamento (FOUC)

Quando a preferência de tema é gerenciada apenas dentro do ciclo de vida de componentes Vue (como no `onMounted`), o navegador primeiro renderiza o HTML padrão do servidor (geralmente escuro ou claro) e apenas frações de segundo depois altera a classe `.dark`. O resultado é um piscar incômodo a cada recarregamento de página.

Para eliminar esse comportamento, a leitura do `localStorage` e a verificação do `prefers-color-scheme` devem ocorrer de forma **síncrona e bloqueante** no `<head>` do documento, antes da execução de qualquer framework ou folha de estilo.

```html
<!-- resources/views/app.blade.php -->
<script>
    (function() {
        try {
            var tema_salvo = localStorage.getItem('techpulse_tema');
            var prefere_escuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (tema_salvo === 'dark' || (!tema_salvo && prefere_escuro)) {
                document.documentElement.classList.add('dark');
            } else if (tema_salvo === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>
```

---

## 2. Configurando o Tailwind CSS com Estratégia de Classe

O Tailwind CSS suporta a diretiva `darkMode: 'class'`, o que nos permite controlar manualmente quando o tema escuro está ativado simplesmente adicionando ou removendo a classe `.dark` no elemento raiz (`<html>`).

```javascript
// tailwind.config.js
export default {
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/js/**/*.{vue,js}",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#2b52ee",
                background: "#001247",
                "on-background": "#f6f6f8",
                // ...
            }
        }
    }
}
```

---

## 3. Criando o Composable Reativo (`use_theme`)

Para que diferentes partes da interface — como a barra superior pública ([`Navbar.vue`](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Components/Navbar.vue)) e o painel administrativo ([`Topbar.vue`](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Components/Admin/Topbar.vue)) — compartilhem o mesmo estado sem duplicação de lógica, criamos um composable desacoplado.

```javascript
// resources/js/Composables/use_theme.js
import { ref } from "vue";

const tema_atual = ref("dark");

const sincronizar_tema_do_dom = () => {
    if (typeof document !== "undefined") {
        tema_atual.value = document.documentElement.classList.contains("dark") ? "dark" : "light";
    }
};

export function use_theme() {
    const alternar_tema = () => {
        if (typeof document === "undefined") return;

        const proximo_tema = tema_atual.value === "dark" ? "light" : "dark";
        definir_tema(proximo_tema);
    };

    const definir_tema = (novo_tema) => {
        tema_atual.value = novo_tema;

        if (typeof document !== "undefined") {
            if (novo_tema === "dark") {
                document.documentElement.classList.add("dark");
            } else {
                document.documentElement.classList.remove("dark");
            }
        }

        if (typeof localStorage !== "undefined") {
            try {
                localStorage.setItem("techpulse_tema", novo_tema);
            } catch (e) {
                // Tratamento seguro para contextos sem permissão de storage
            }
        }
    };

    const inicializar_tema = () => {
        sincronizar_tema_do_dom();
    };

    return {
        tema_atual,
        alternar_tema,
        definir_tema,
        inicializar_tema,
    };
}
```

---

## 4. Integrando o Botão de Alternância na Navbar

Com o composable pronto, incluímos o botão interativo com acessibilidade total (`aria-label`, foco visível e ícones expressivos do Material Symbols):

```vue
<!-- Trecho em resources/js/Components/Navbar.vue -->
<template>
  <nav class="glass-header fixed top-0 w-full z-50 ...">
    <!-- Links e logo -->
    <div class="flex items-center gap-3 sm:gap-4">
      <button
        type="button"
        class="text-on-surface-variant hover:text-primary hover:bg-primary/10 transition-all duration-300 p-2 rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        :aria-label="label_tema"
        :title="label_tema"
        @click="alternar_tema"
      >
        <span class="material-symbols-outlined">{{ icone_tema }}</span>
      </button>
      <!-- ... -->
    </div>
  </nav>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { use_theme } from "@/Composables/use_theme";

const { tema_atual, alternar_tema, inicializar_tema } = use_theme();

onMounted(() => {
  inicializar_tema();
});

const icone_tema = computed(() => {
  return tema_atual.value === "dark" ? "light_mode" : "dark_mode";
});

const label_tema = computed(() => {
  return tema_atual.value === "dark" ? "Ativar modo claro" : "Ativar modo escuro";
});
</script>
```

---

## 5. Conclusão

Com essa estrutura simples e robusta, alcançamos:
- **Zero FOUC**: O navegador inicia com a classe visual exata do usuário.
- **Persistência confiável**: Preferência mantida entre sessões no `localStorage`.
- **Respeito às preferências do sistema**: Detecção automática de `prefers-color-scheme`.
- **Reatividade e desacoplamento**: O composable pode ser consumido em qualquer componente da aplicação com reatividade síncrona.

