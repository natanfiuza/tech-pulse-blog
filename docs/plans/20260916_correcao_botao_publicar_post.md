# Correção dos Botões na Edição de Post Publicado

Este plano documenta a implementação necessária para ajustar os botões de ação na tela de edição de posts (`PostsEdit.vue`), conforme solicitado:
1. Quando o post **já está publicado** (`status === 'publicado'`), o botão **"Salvar Rascunho"** não deve ser exibido.
2. O botão principal deve ter o texto alterado de **"Publicar"** para **"Salvar Alteração"** quando o post já estiver publicado.
3. Quando o post estiver publicado, o botão **"Salvar Alteração"** deve iniciar **desabilitado** e só ficar **habilitado** quando houver alteração em qualquer um dos campos do formulário (título, conteúdo markdown, resumo, categoria, hashtags, agendamento ou imagem de capa).

---

## User Review Required

> [!NOTE]
> Para posts que **não** estão publicados (por exemplo, posts em estado `rascunho`):
> - Ambos os botões ("Publicar" e "Salvar Rascunho") continuam sendo exibidos normalmente.
> - O botão "Publicar" envia o post com status `publicado`, e "Salvar Rascunho" salva mantendo o status `rascunho`.

---

## Proposed Changes

### Frontend (`resources/js/Pages/Admin/Posts/PostsEdit.vue`)

#### [MODIFY] [PostsEdit.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Pages/Admin/Posts/PostsEdit.vue)

1. **Estado de Publicação e Controle dos Botões:**
   - Criar `computed` `eh_publicado`: verifica se `props.post?.status === 'publicado'`.
   - Adicionar condição `v-if="!eh_publicado"` no botão "Salvar Rascunho".
   - Ajustar o rótulo do botão principal dinamicamente: `{{ eh_publicado ? 'Salvar Alteração' : 'Publicar' }}`.

2. **Detecção Reativa de Alterações (`houve_alteracao`):**
   - Comparar de forma reativa os valores atuais com os valores iniciais do post:
     - `form.title` vs `props.post?.title`
     - `original_content.value` vs `props.post?.content`
     - `form.excerpt` vs `props.post?.excerpt`
     - `form.category_id` vs `props.post?.category_id`
     - `form.published_at` vs `published_at_inicial`
     - `form.hashtags` vs `hashtags_iniciais`
     - `form.image !== null` (se selecionou nova imagem)
   - Desabilitar o botão de ação quando `form.processing || (eh_publicado && !houve_alteracao)`.

3. **Garantia de Convenções (`CLAUDE.md`):**
   - Nomenclatura 100% em `snake_case` (`eh_publicado`, `houve_alteracao`, `hashtags_iniciais`, etc.).
   - Sem uso de `console.log`.

---

## Survey & Documentação de Domínio

#### [NEW] [20260916_edicao_post_botoes_publicado_surveyfacts.md](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/docs/survey/20260916_edicao_post_botoes_publicado_surveyfacts.md)
- Registro do levantamento de fatos e regras de negócio para a edição de posts.

#### [NEW] [20260916_edicao_post_botoes_publicado.md](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/docs/claude-code/reports/develop_natan/20260916_edicao_post_botoes_publicado.md)
- Relatório final da tarefa na pasta de relatórios da branch `develop_natan`.

---

## Verification Plan

### Automated Build Verification
- Rodar `npm run build` para certificar a compilação do Vite sem erros.

### Manual Verification
- Testar a interface na tela de edição (`/admin/posts/edit/{uuid}`):
  - Post já publicado:
    - Botão "Salvar Rascunho" ausente.
    - Botão "Salvar Alteração" visível e inicialmente desabilitado.
    - Ao digitar no título, editor markdown, resumo, trocar categoria, alterar tags, data ou imagem, o botão "Salvar Alteração" é habilitado.
    - Ao restaurar o valor original, o botão volta a ficar desabilitado.
    - Ao submeter a alteração, o salvamento ocorre com sucesso.
  - Post rascunho:
    - Exibe ambos os botões ("Publicar" e "Salvar Rascunho").
