# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona seleção de categoria pai em criar e editar categoria
```

---

## 🎯 Resumo
Permite escolher explicitamente a categoria pai nos formulários de criação e edição de categorias, eliminando a necessidade de inferir hierarquia manualmente. Na edição, a própria categoria é excluída das opções para impedir auto-referência (categoria pai de si mesma), e a árvore é achatada no frontend com recuo visual por nível, preservando a leitura hierárquica no `select`.

## 🛠️ Mudanças Técnicas
- `CategoryController@create`: carrega lista plana de categorias (`id`, `name`) ordenada por nome para popular o dropdown de categoria pai.
- `CategoryController@edit`: busca categorias candidatas a pai excluindo o próprio registro (`where('id', '!=', $category->id)`), ordenadas por nome.
- `CategoriesCreate.vue`: renderiza as opções de categorias no `select` de categoria pai, além da opção `-- Nenhuma --`.
- `CategoriesEdit.vue`: adiciona `computed categorias_planas`, que achata a árvore (raiz → filho → neto), aplica recuo com `\u00a0` repetido por nível e negrito para nível 0.
- `ModalCategoriasGuia.spec.js`: atualiza a expectativa de contagem de tags `strong` para incluir negritos de Markdown e torna a checagem de labels tolerante a múltiplas ocorrências.

## ⚠️ Impacto/Avisos
- Sem migrações, alterações de schema, novas variáveis de ambiente ou dependências.
- Contrato de props das páginas Inertia de categorias muda: `create` passa a expor lista plana e `edit` expõe os pais disponíveis. Consumidores dessas props precisam ser revisados.
- Revisar o diff do controller antes do merge: há indícios de linhas duplicadas, encadeamento de `->get()` após `;` e chave `'category'` repetida no array de `Inertia::render`, o que pode causar erro de sintaxe ou comportamento inesperado. Rodar `php -l` e a suíte de testes.
- A regra de negócio ainda permite hierarquia profunda, mas o flatten cobre apenas 3 níveis (raiz, filho, neto); níveis adicionais ficarão fora do dropdown.

".gitpr.commit.md": "gitpr.commit.md"

close #136

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)