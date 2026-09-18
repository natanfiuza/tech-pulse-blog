# Relatório de Tarefa — Busca Dinâmica no Admin (Posts e Categorias)

**Data:** 18 de setembro de 2026  
**Branch:** `develop_natan`  
**Tarefa:** Aplicar filtragem dinâmica na tela de gerenciamento de Posts e de Categorias utilizando a caixa de busca presente na barra superior (`Topbar.vue`). Na busca de posts, estender os critérios para título, tags, resumo (excerpt) e corpo do post (content); na busca de categorias, restringir ao nome.

---

## 1. Resumo das Modificações

### Helper [helpers.js](../../../../resources/js/helpers.js)
- Adicionada a função `normalizar_texto(texto)` para converter strings em minúsculas e remover acentos via decomposição Unicode (`NFD`).

### Composable [use_admin_busca.js](../../../../resources/js/Composables/use_admin_busca.js)
- Criado composable com estado reativo compartilhado `termo_busca` (`ref("")`).
- Implementado `limpar_busca()` para resetar o termo.
- Implementado `filtrar_posts(posts)` que busca de forma insensível a maiúsculas e acentos nos campos: título (`title`), hashtags (`hashtags.name` e `hashtags.slug`), resumo (`excerpt`) e corpo do post (`content`).
- Implementado `filtrar_categorias(categories)` que busca pelo nome da categoria (`name`).

### Barra Superior [Topbar.vue](../../../../resources/js/Components/Admin/Topbar.vue)
- Conectado o `v-model` do `<input type="search">` com `termo_busca`.
- Adicionado placeholder contextual dinâmico (`Buscar posts...` em Posts, `Buscar categorias...` em Categorias e `Buscar...` nas demais telas).

### Layout [AdminLayout.vue](../../../../resources/js/Layouts/AdminLayout.vue)
- Integrada limpeza automática do `termo_busca` ao navegar entre rotas (`$watch` em `$page.url`).

### Tela [PostsIndex.vue](../../../../resources/js/Pages/Admin/Posts/PostsIndex.vue)
- Integrado `use_admin_busca` com propriedade computada `posts_filtrados`.
- Atualizado contador de posts para refletir a quantidade de registros filtrados.
- Atualizada mensagem de estado vazio para indicar quando a busca não encontrou resultados para o termo digitado.

### Tela [CategoriesIndex.vue](../../../../resources/js/Pages/Admin/Categories/CategoriesIndex.vue)
- Integrado `use_admin_busca` com propriedade computada `categories_filtradas`.
- Atualizado contador de categorias para refletir a quantidade de registros filtrados.
- Atualizada mensagem de estado vazio para indicar quando a busca não encontrou resultados para o termo digitado.

---

## 2. Validação

- `npm run build` executado com sucesso (código 0).
- Testes automatizados executados via PHPUnit com sucesso.
- Convenções de código verificadas: snake_case rigoroso em funções/variáveis JS e ausência de `console.log` / `dd()`.
- Zero classes residuais do Bootstrap.

