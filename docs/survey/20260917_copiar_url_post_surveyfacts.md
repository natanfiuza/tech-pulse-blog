# Survey de Fatos — Cópia e Visualização da URL na Listagem de Posts

**Data:** 17 de setembro de 2026  
**Contexto:** Listagem de posts no painel administrativo (`PostsIndex.vue`).

---

## 1. Problema e Motivação

- Ao criar novos posts (principalmente no estado de **rascunho**), os autores/administradores não visualizavam o slug gerado nem tinham um atalho direto para obter a URL do post.
- Para obter a URL gerada, era necessário deduzir manualmente ou acessar o banco de dados.

---

## 2. Decisões de Design e Comportamento

- **Exibição:** Badge interativo mono exibindo `/post/show/{slug}` abaixo do título de cada post no card da listagem.
- **Ação:** Clique no badge copia a URL completa (`${window.location.origin}/post/show/${post.slug}`) para a área de transferência do usuário.
- **Feedback:** Alteração do ícone (`content_copy` para `check`) e exibição de etiqueta `"Copiado!"` temporária por 2 segundos.
- **Resiliência:** Uso prioritário de `navigator.clipboard.writeText` com fallback via elemento textarea temporário para compatibilidade com contextos inseguros ou navegadores antigos.

