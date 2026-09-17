# Survey de Fatos — Habilitação do Botão "Salvar Alteração" ao Modificar Tags na Edição de Post

**Data:** 16 de setembro de 2026  
**Contexto:** Investigação e levantamento de regras para habilitar o botão "Salvar Alteração" ao modificar o campo de tags na edição de post (`resources/js/Pages/Admin/Posts/PostsEdit.vue` e `resources/js/Components/Admin/TagInput.vue`).

---

## 1. Diagnóstico do Problema

1. **Estado do botão "Salvar Alteração" na edição de post publicado:**
   - Em `PostsEdit.vue`, o botão fica desabilitado quando `form.processing || (eh_publicado && !houve_alteracao)`.
   - A computada `houve_alteracao` compara `form.hashtags` com `hashtags_iniciais`.

2. **Causa Raiz 1 (Texto pendente no input de tags):**
   - No componente `TagInput.vue`, o texto digitado pelo usuário fica armazenado localmente em `texto.value` antes de ser confirmado via `Enter`, `Tab`, delimitador (`,`, `;`) ou perda de foco (`blur`).
   - Enquanto o usuário está digitando no campo de tags, `form.hashtags` ainda não foi modificado. Como o botão "Salvar Alteração" permanece com `:disabled="true"`, o usuário não consegue clicar nele diretamente (o clique em botões desabilitados é bloqueado pelo navegador antes do blur acontecer adequadamente).
   - O campo de tags precisa comunicar a existência de texto pendente (`texto_pendente`) para que `houve_alteracao` reaja imediatamente à digitação.

3. **Causa Raiz 2 (Flush / Confirmação de tag ao submeter):**
   - Ao submeter o formulário (`submit`), se houver qualquer texto pendente no campo de entrada do `TagInput`, ele deve ser adicionado/confirmado automaticamente antes de enviar o payload para a rota `posts.update`.

4. **Causa Raiz 3 (Robustez na comparação de arrays de tags):**
   - A normalização e comparação de `form.hashtags` contra `hashtags_iniciais` deve suportar objetos ou strings, com tratamento insensível a maiúsculas/minúsculas e sem espaços extras nas extremidades.

---

## 2. Decisões de Implementação

1. **`TagInput.vue`:**
   - Adicionar sincronização do texto pendente via evento `update:texto_pendente` / prop `texto_pendente` (ou `v-model:texto_pendente`).
   - Emitir `update:model_value` e `update:modelValue` para conformidade total com Vue 3.
   - Expor método `confirmar_texto()` (via `expose` / `defineExpose`) para inclusão forçada de tag pendente caso o submit ocorra.

2. **`PostsEdit.vue`:**
   - Vincular `v-model:texto_pendente="texto_pendente_tag"` ao `TagInput` com `ref="tag_input_ref"`.
   - Incluir `(texto_pendente_tag.trim() !== '')` na verificação da computada `houve_alteracao`.
   - Na função `submit()`, chamar `tag_input_ref.value?.confirmar_texto()` para que qualquer tag digitada seja adicionada ao `form.hashtags` antes do envio.
   - Reforçar `arrays_sao_iguais` para comparação limpa de arrays de tags.

3. **Conformidade (`CLAUDE.md` e `CONTEXT.md`):**
   - Todo o código e variáveis em `snake_case` (`texto_pendente_tag`, `tag_input_ref`, `confirmar_texto`).
   - Sem `console.log`.

