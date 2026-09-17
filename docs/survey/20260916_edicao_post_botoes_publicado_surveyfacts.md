# Survey — Correção dos Botões na Edição de Post Publicado (2026-09-16)

> Levantamento de fatos e regras de domínio da sessão `/grill-with-docs` para a tela de edição de posts (`PostsEdit.vue`).

## 1. Contexto da tarefa
- **Pedido original:** Corriga a edição de Post, quando clica em editar e o post ja esta publicado o botão Salvar Rascunho não deve ser exibido e o botão Publicar deve ser trocado para Salvar Alteração, e deve ficar desabilitado e quando algo for alterado fica habilitado.
- **Tela / Módulo:** Painel Admin — Edição de Post (`/admin/posts/edit/{uuid}`, `resources/js/Pages/Admin/Posts/PostsEdit.vue`, `PostController.php`).
- **Skill:** `/grill-with-docs` · branch `develop_natan`
- **Arquivos-chave:**
  - `resources/js/Pages/Admin/Posts/PostsEdit.vue`
  - `app/Http/Controllers/PostController.php`
  - `app/Models/Post.php`

## 2. Regras de Domínio e Decisões

### Estado Publicado (`status === 'publicado'`)
| Elemento | Comportamento |
|---|---|
| Botão "Salvar Rascunho" | **Oculto** (`v-if="!eh_publicado"`). Um post publicado não pode ser revertido para rascunho por este botão na edição. |
| Botão Principal | Texto dinâmico: **"Salvar Alteração"** (em rascunhos permanece **"Publicar"**). |
| Habilitação / Desabilitação | Inicialmente **desabilitado** (`disabled`). Fica **habilitado** assim que qualquer campo do post for alterado. |

### Detecção Reativa de Alterações (`houve_alteracao`)
Para garantir rastreamento de mudanças sem falsos positivos:
- **Título:** `form.title` vs `props.post.title`
- **Conteúdo Markdown:** `original_content` vs `props.post.content`
- **Resumo:** `form.excerpt` vs `props.post.excerpt`
- **Categoria:** `form.category_id` vs `props.post.category_id`
- **Data de Publicação / Agendamento:** `form.published_at` vs `props.post.published_at` formatada
- **Hashtags:** comparação de arrays ordenada (`form.hashtags` vs `props.post.hashtags`)
- **Imagem de Capa:** `form.image !== null`

## 3. Conformidade com Convenções do Repositório (`CLAUDE.md`)
- Funções e variáveis 100% em `snake_case` (`eh_publicado`, `houve_alteracao`, `hashtags_iniciais`, `arrays_sao_iguais`).
- Ausência de `console.log` no frontend.
- Build de produção gerado e validado com sucesso via `npm run build`.

