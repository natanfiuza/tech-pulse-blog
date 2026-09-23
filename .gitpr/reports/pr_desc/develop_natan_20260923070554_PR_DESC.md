# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige indentação e nome duplicado nos selects de categoria
```

---

## 🎯 Resumo

Os selects de categoria dos formulários de criação e edição de posts exibiam o nome de cada categoria duplicado e com indentação inconsistente. Como elementos `<option>` ignoram `padding-left` em parte dos navegadores, a hierarquia de categorias (categoria pai/filha) não era percebida visualmente pelo usuário, dificultando a escolha correta durante a publicação.

Esta mudança garante que a árvore de categorias seja legível de forma nativa no dropdown, usando espaçamento real por indentação em vez de CSS.

## 🛠️ Mudanças Técnicas

- Substitui a indentação via `paddingLeft` inline por caracteres não separáveis (`\u00a0`) repetidos conforme `categoria.nivel`, preservando o alinhamento dentro do `<option>`.
- Remove a renderização duplicada de `{{ categoria.name }}`, que fazia o nome da categoria aparecer duas vezes no select.
- Mantém apenas o destaque em negrito (`fontWeight`) para categorias de nível raiz.
- Alterações aplicadas em `resources/js/Pages/Admin/Posts/PostsCreate.vue` e `resources/js/Pages/Admin/Posts/PostsEdit.vue`, mantendo os dois fluxos consistentes.

## ⚠️ Impacto/Avisos

- **Escopo:** alteração exclusivamente de frontend (Vue 3). Sem mudanças em banco de dados, rotas, API pública ou variáveis de ambiente.
- **Dependências:** nenhuma dependência adicionada, removida ou atualizada.
- **Compatibilidade:** o uso de `\u00a0` é suportado por todos os navegadores modernos e por leitores de tela (o texto lido permanece o nome da categoria).
- **Validação recomendada:** conferir visualmente os selects de criação e edição com categorias de múltiplos níveis aninhados.

close #132

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)