# Survey de Fatos — Correção e Aprimoramento do TagInput

**Data:** 16 de setembro de 2026  
**Contexto:** Investigação sobre o comportamento de salvamento de tags/hashtags na criação e edição de posts (`PostsCreate.vue`, `PostsEdit.vue`, `TagInput.vue` e `PostController.php`).

---

## 1. Fatos Técnicos Verificados

### Backend (`PostController.php`)
- O método `store` processa a sincronização de hashtags via `sincronizar_hashtags($post, $request->input('hashtags', []))`.
- O método `update` também executa a sincronização de hashtags via `sincronizar_hashtags($post, $request->input('hashtags', []))`.
- As tags são armazenadas na tabela `hashtags` com seus respectivos slugs únicos (`criar_slug`) e associadas na tabela pivô `post_hashtag` por `$post->hashtags()->sync($ids)`.
- Testes programáticos confirmaram que ambos os métodos (`store` e `update`) persistem as tags no banco de dados quando o array `hashtags` é enviado no payload.

### Frontend (`TagInput.vue`, `PostsCreate.vue` e `PostsEdit.vue`)
- **Causa Raiz 1 (Ausência de commit no blur):** O componente `TagInput` apenas adicionava o texto digitado ao array de tags (`model_value`) se o usuário pressionasse `Enter` ou clicasse em uma sugestão. Se o usuário digitasse a tag e clicasse diretamente no botão "Salvar Alteração" ou "Publicar", o evento `blur` apenas fechava a lista de sugestões sem adicionar o texto pendente, fazendo com que o formulário fosse enviado sem a nova tag.
- **Causa Raiz 2 (Ausência de suporte a delimitadores comuns):** Usuários frequentemente utilizam vírgula (`,`), ponto-e-vírgula (`;`), tecla `Tab` ou colagem de texto delimitado para separar tags. O componente não interceptava esses caracteres.
- **Causa Raiz 3 (Termos compostos):** Tags com termos compostos contendo espaço (ex.: `Inteligência Artificial`, `Clean Code`) devem ser preservadas, razão pela qual a vírgula (`,`), e não o espaço, é o delimitador ideal.

---

## 2. Decisão de Design
- Manter o suporte a nomes de tags contendo espaços (termos compostos).
- Adicionar suporte a vírgula (`,`), ponto-e-vírgula (`;`), tecla `Tab`, quebras de linha e perda de foco (`blur`) para confirmação automática de tags.
- Manter convenção de nomes `snake_case` e sem `console.log`.

