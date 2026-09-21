# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige contraste do editor markdown no tema claro
```

---

## 🎯 Resumo

O editor Markdown (EasyMDE/CodeMirror) foi originalmente estilizado apenas para o tema escuro "Midnight Pulse". Ao alternar para o tema claro, o texto do editor herdava cores claras sobre fundos claros, tornando a escrita praticamente ilegível para administradores que usam o tema claro. Esta mudança aplica um conjunto explícito de cores de contraste para o tema claro, além de corrigir o fundo do painel de preview no tema escuro, que estava transparente e permitia que o conteúdo de trás aparecesse através do preview.

## 🛠️ Mudanças Técnicas

- Adiciona regras em `resources/css/tailwind.css` para `html:not(.dark)` cobrindo texto do `CodeMirror`, placeholders, toolbar (fundo, botões, hover/active e separadores) e painéis de preview.
- Adiciona em `resources/js/Components/Admin/MarkdownEditor.vue` um bloco de estilos `:global(html:not(.dark))` com as mesmas adaptações para o tema claro, incluindo seleção de texto, modo fullscreen e a mensagem de aviso do editor.
- Corrige o fundo de `.editor-preview-side` no tema escuro, trocando `transparent` por `#000b2b` (cor sólida do tema Midnight Pulse).
- Reorganiza os comentários do bloco `<style scoped>`, separando claramente as seções "Tema Dark (Midnight Pulse)" e "Adaptação para Tema Claro".
- Não há alteração de lógica, props, eventos ou estrutura de componentes — apenas estilos.

## ⚠️ Impacto/Avisos

- **Banco de dados:** sem migrações ou alterações de schema.
- **Variáveis de ambiente:** nenhuma adição ou alteração de `.env`.
- **Dependências:** nenhuma nova dependência ou atualização de `package.json`/`composer.json`.
- **Assets:** requer rebuild do front-end (`npm run build` ou `npm run dev`) para que o CSS compilado reflita as mudanças; sem isso, a correção não aparece em produção.
- **Manutenção:** as regras do tema claro existem em dois lugares (CSS global e style scoped do componente), com uso frequente de `!important`. Isso é intencional para vencer a especificidade do EasyMDE, mas é um ponto de duplicação a considerar em refatorações futuras.
- **Risco:** mudança puramente visual, restrita ao editor de artigos no painel admin; baixo risco de regressão funcional.


close #116

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)