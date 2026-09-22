# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona guia de categorias no admin de posts
```

---

## 🎯 Resumo
Disponibiliza um guia visual de categorias no admin de posts para consulta rápida da hierarquia e metadados, reduzindo erros de classificação e a necessidade de sair da tela de gerenciamento.

## 🛠️ Mudanças Técnicas
- `PostController@index` passa `categorias` para a view, obtidas via `categorias_para_select()`.
- Cria o componente `ModalCategoriasGuia.vue` com busca, expansão/recolhimento e suporte a até três níveis de categorias.
- Exibe descrição, abrangência, possíveis conteúdos e sugestões de postagens por categoria.
- Adiciona botão "Categorias" e o estado `modal_categorias_aberto` em `PostsIndex.vue`.
- Registra e integra o modal ao `AdminLayout` da listagem de posts.

## ⚠️ Impacto/Avisos
- Sem alterações em banco de dados, variáveis de ambiente ou dependências.
- A view depende da prop `categorias` e do método `categorias_para_select()` retornando a estrutura hierárquica esperada.
- O modal é apenas de consulta; não altera persistência de posts ou categorias.
- Metadados de automação: ".gitpr.commit.md": "gitpr.commit.md".


close #127

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)