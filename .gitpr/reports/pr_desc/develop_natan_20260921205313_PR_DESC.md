# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige link do navbar e ajusta contraste dos textos
```

---

## 🎯 Resumo

O botão de destaque da navbar apontava para a página de newsletter, direcionando o visitante para uma rota diferente da jornada de conversão esperada. Esta alteração corrige o destino do CTA para a página de cadastro (`/register`), garantindo que o clique principal da navegação leve o usuário ao fluxo de registro.

Em paralelo, foram ajustados os tons de texto dos cards informativos da página de newsletter para melhorar a legibilidade e o contraste, tanto no tema claro quanto no escuro, atendendo a boas práticas de acessibilidade visual.

## 🛠️ Mudanças Técnicas

- Atualiza o `href` do CTA principal em `resources/js/Components/Navbar.vue` de `/newsletter` para `/register`.
- Ajusta as classes de cor de título em `resources/js/Pages/NewsletterPage.vue` de `text-slate-900` para `text-slate-800`.
- Ajusta as classes de cor de descrição em `NewsletterPage.vue` de `text-on-surface-variant` para `text-slate-600`.
- Mantém as variantes de tema escuro (`dark:`) inalteradas, preservando o comportamento atual no modo dark.

## ⚠️ Impacto/Avisos

- **Navegação:** o CTA da navbar deixa de levar à newsletter e passa a levar ao cadastro; verifique se a rota `/register` está exposta conforme o fluxo de produto desejado.
- **Sem mudanças em banco de dados, variáveis de ambiente ou dependências.**
- Alteração restrita a estilos e atributos de link no frontend, sem impacto na API pública consumida pelo app Flutter.

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)