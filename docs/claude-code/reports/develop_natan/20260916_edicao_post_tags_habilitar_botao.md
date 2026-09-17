# Relatório de Tarefa — Habilitação do Botão Salvar Alteração com Campo Tags

**Data:** 16 de setembro de 2026  
**Branch:** `develop_natan`  
**Tarefa:** Habilitar imediatamente o botão "Salvar Alteração" ao alterar ou digitar no campo de tags na edição de posts (`PostsEdit.vue`), e auto-confirmar tags pendentes no envio.

---

## 1. Resumo das Modificações

### Componente [TagInput.vue](../../../../resources/js/Components/Admin/TagInput.vue)
- Adicionada prop `texto_pendente` e emit `update:texto_pendente` sincronizado reativamente com o input de digitação.
- Adicionado suporte a `modelValue` e `update:modelValue` em paralelo a `model_value` para compatibilidade total com o ecossistema Vue 3.
- Exposto o método `confirmar_texto()` (via `expose`) para que formulários pais possam comitar qualquer tag pendente antes da submissão do formulário.

### Tela [PostsEdit.vue](../../../../resources/js/Pages/Admin/Posts/PostsEdit.vue)
- Vinculado `v-model:texto_pendente="texto_pendente_tag"` e `ref="tag_input_ref"` ao componente `TagInput`.
- Ajustada a propriedade computada `houve_alteracao` para considerar tanto mudanças no array de tags (`form.hashtags`) quanto texto pendente sendo digitado (`texto_pendente_tag.trim() !== ''`).
- Aprimorada a função de comparação `arrays_sao_iguais` e inicialização de `hashtags_iniciais` para garantir normalização uniforme de tags existentes.
- Na função `submit()`, adicionada a invocação de `tag_input_ref.value?.confirmar_texto()` antes de enviar a requisição `posts.update`.

### Tela [PostsCreate.vue](../../../../resources/js/Pages/Admin/Posts/PostsCreate.vue)
- Adicionado `ref="tag_input_ref"` e chamada de `tag_input_ref.value?.confirmar_texto()` na submissão de novos posts.

---

## 2. Documentos Relacionados

- Survey de fatos: [20260916_edicao_post_tags_surveyfacts.md](../../survey/20260916_edicao_post_tags_surveyfacts.md)

---

## 3. Validação

- Execução de `npm run build` concluída com sucesso (código 0).
- Nomenclatura em estrito `snake_case` e ausência de `console.log`.

