# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: renderiza markdown nas categorias e adiciona testes Vitest
```

---

## 🎯 Resumo

Os campos descritivos da árvore de categorias (descrição, abrangência, conteúdos possíveis e sugestões de postagens) passam a ser exibidos com formatação Markdown, tornando as orientações de conteúdo legíveis dentro do modal administrativo em vez de texto puro com asteriscos e crases. Para dar segurança a essa mudança e evitar regressões silenciosas na renderização hierárquica, foi introduzida uma base de testes de componentes com Vitest, cobrindo o parsing de Markdown e a estrutura raiz/filho.

## 🛠️ Mudanças Técnicas

- Adiciona `markdown-it` em `ModalCategoriasGuia.vue` com helper `renderMarkdown` aplicado via `v-html` nos quatro campos textuais da categoria (raiz, filho e neto).
- Substitui os rótulos em texto simples por `<strong>` (ex.: `Descrição:`, `Sugestões de Postagens:`) e padroniza o rótulo de sugestões no nível filho.
- Configura `vitest.config.js`/`vitest.config.ts` com ambiente `jsdom` e plugin Vue, e adiciona o script `npm run test` (`vitest run`).
- Adiciona `vitest`, `@vue/test-utils`, `jsdom` e `@testing-library/jest-dom` como devDependencies.
- Cria `tests/Components/Admin/ModalCategoriasGuia.spec.js` validando conversão de Markdown (`em`, `strong`, `code`), contagem de rótulos e expansão da hierarquia.
- Ajusta `PostsCreate.vue` e `PostsEdit.vue` com estilo inline no `<option>` (negrito para nível 0 e indentação para níveis filhos).

## ⚠️ Impacto/Avisos

- **Dependência não declarada:** `markdown-it` é importado em `ModalCategoriasGuia.vue`, mas não consta em `package.json` neste diff — o build quebra em instalação limpa até que seja adicionado.
- **Possível regressão em Posts/PostsCreate/PostsEdit:** o nome da categoria está sendo interpolado duas vezes dentro do `<option>` (`{{ "  ".repeat(categoria.nivel) }}{{ categoria.name }}` seguido de `{{ categoria.name }}`), duplicando o texto exibido no select.
- **Segurança:** o uso de `v-html` com Markdown permite injeção de HTML. Restrinja a origem dos dados (hoje admin-only) ou habilite `html: false`/sanitização no `MarkdownIt`.
- **Configuração duplicada:** `vitest.config.js` e `vitest.config.ts` foram criados com conteúdo idêntico; mantenha apenas um para evitar ambiguidade de resolução.
- Sem alterações em banco de dados, variáveis de ambiente ou migrations. A API pública consumida pelo app Flutter não é afetada.

close #130

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)