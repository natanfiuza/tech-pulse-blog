# Relatório de Tarefa — Cópia de URL na Listagem de Posts

**Data:** 17 de setembro de 2026  
**Branch:** `develop_natan`  
**Tarefa:** Adicionar botão/badge na listagem de Posts (`PostsIndex.vue`) para visualizar o slug gerado e permitir copiar a URL completa para a área de transferência com feedback instantâneo.

---

## 1. Resumo das Modificações

### Tela [PostsIndex.vue](../../../../resources/js/Pages/Admin/Posts/PostsIndex.vue)
- Adicionado um badge interativo em fonte mono exibindo `/post/show/{post.slug}` logo abaixo do título do post em cada card.
- Adicionado botão integrado ao badge com ícone de cópia (`content_copy`) e tooltip informativo com a URL completa.
- Implementado suporte reativo para feedback de cópia com ícone de confirmação (`check`) e tag `"Copiado!"` por 2 segundos.
- Adicionado método `obter_url_completa(post)` para construir a URL absoluta dinâmica utilizando `window.location.origin`.
- Implementado método `copiar_url_post(post)` com suporte prioritário à API moderna `navigator.clipboard.writeText` e método de contingência `copiar_fallback(texto, uuid)` via elemento temporário para compatibilidade com qualquer contexto de execução.
- Adicionado `marcar_copiado(uuid)` com limpeza de timeout para garantir controle de estado sem vazamentos de memória.

---

## 2. Documentos Relacionados

- Survey de fatos: [20260917_copiar_url_post_surveyfacts.md](../../survey/20260917_copiar_url_post_surveyfacts.md)

---

## 3. Validação

- Execução de `npm run build` concluída com sucesso (código 0).
- Nomenclatura em estrito `snake_case` e ausência de `console.log` / `dd()`.
- Sem URLs hardcoded de desenvolvimento (`localhost`/`127.0.0.1`).

