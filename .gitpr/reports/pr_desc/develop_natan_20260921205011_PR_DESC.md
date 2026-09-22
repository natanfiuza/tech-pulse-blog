# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige contraste da newsletter no modo claro
```

---

## 🎯 Resumo

A página de newsletter e o componente `NewsletterBox` exibiam textos e superfícies com cores pensadas apenas para o tema escuro. No modo claro, elementos como mensagens de status, títulos de benefícios e o campo de e-mail ficavam com contraste insuficiente, prejudicando a legibilidade e a acessibilidade. Este ajuste torna a interface consistente e legível nos dois temas, sem alterar comportamento ou regras de negócio.

## 🛠️ Mudanças Técnicas

- Adiciona variantes `dark:` nos textos de status do `NewsletterBox` (`text-slate-800 dark:text-slate-200`) para mensagens de envio, link de cancelamento e confirmação.
- Ajusta o link de cancelamento para `text-amber-600 dark:text-amber-300` e estados de hover correspondentes por tema.
- Corrige o input de inscrição para `text-slate-900 dark:text-slate-100`, garantindo contraste do texto digitado no modo claro.
- Atualiza os títulos de benefícios em `NewsletterPage.vue` para `text-slate-900 dark:text-slate-200`.
- Refina o container do formulário com `bg-white dark:bg-surface-dim/70`, bordas e sombras específicas por tema.
- Ajusta o link de retorno para usar `hover:text-primary` no modo claro e `dark:hover:text-white` no escuro.
- Amplia as regras utilitárias em `resources/css/tailwind.css`: inclusão de `.text-slate-200` e do seletor `[class*="bg-surface-dim/"]` no override de cores do tema claro.

## ⚠️ Impacto/Avisos

- **Sem alterações de banco de dados**, variáveis de ambiente ou dependências.
- Mudança puramente visual (CSS/Vue), com impacto restrito às páginas de newsletter.
- Leve aumento do CSS gerado pela inclusão de novas regras/seletores utilitários; sem impacto perceptível de performance.
- Recomenda-se validação visual manual nos temas claro e escuro para confirmar contraste.

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)