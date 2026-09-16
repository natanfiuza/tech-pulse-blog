# Relatório de Tarefa — Correção dos Botões na Edição de Post Publicado

**Data:** 16 de setembro de 2026  
**Branch:** `develop_natan`  
**Tarefa:** Ajustar botões de ação e detecção de alterações na tela de edição de posts (`PostsEdit.vue`).

---

## 1. Resumo das Modificações

No arquivo [PostsEdit.vue](../../../../resources/js/Pages/Admin/Posts/PostsEdit.vue):

1. **Visibilidade do botão "Salvar Rascunho":**
   - Adicionada condição `v-if="!eh_publicado"` para que o botão de salvar rascunho não seja exibido quando o post já está publicado.
2. **Rótulo do botão de ação:**
   - Alterado para exibir `"Salvar Alteração"` se o post for publicado (`eh_publicado`), ou `"Publicar"` caso contrário.
3. **Estado desabilitado com base em alterações:**
   - Adicionada propriedade computada `houve_alteracao` que compara os valores atuais do formulário com os valores iniciais vindos de `props.post`.
   - O botão fica desabilitado quando `form.processing || (eh_publicado && !houve_alteracao)`.
4. **Proteção no envio (`submit`):**
   - Se o post estiver publicado e nenhuma alteração foi realizada, a função `submit` retorna imediatamente sem disparar a requisição.

---

## 2. Documentos Relacionados

- Survey de fatos: [20260916_edicao_post_botoes_publicado_surveyfacts.md](../../survey/20260916_edicao_post_botoes_publicado_surveyfacts.md)
- Plano aprovado: [20260916_correcao_botao_publicar_post.md](../../plans/20260916_correcao_botao_publicar_post.md)

---

## 3. Validação

- Execução de `npm run build` concluída com sucesso (código 0).
- Verificação de convenções de código (`snake_case` rigoroso, sem `console.log`).

