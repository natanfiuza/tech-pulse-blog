# Relatório de Tarefa — Imagens de Conteúdo no Editor de Posts

**Data:** 17 de setembro de 2026
**Branch:** `develop_natan`
**Tarefa:** Permitir o upload de imagens direto no editor de posts, no estilo do editor de issues do GitHub — colar ou arrastar um arquivo envia na hora, o arquivo recebe um uuid e é salvo em `storage/app/data/post_content_images`, servido em `/post/content/images/{slug_do_titulo}/{uuid}` — com inserção **na posição do cursor** e exclusão física da imagem que sair do texto ao salvar.

---

## 1. Resumo das Modificações

### Backend

- **Novo** [config/techpulse.php](../../../../config/techpulse.php) — `url_publica` (default de produção, sobrescrevível por `TECHPULSE_URL_PUBLICA`) e `imagens_conteudo` (`data/post_content_images`). Existe porque `config('app.url')` é `http://localhost:8000` no `.env` local e gravaria localhost dentro do banco.
- **Novo** [app/Services/ImagensDeConteudo.php](../../../../app/Services/ImagensDeConteudo.php) — única fonte das regras: `diretorio()`, `url_absoluta()`, `uuids_referenciados()`, `sincronizar()`, `remover_todas()`. A extração de uuid casa pelo **caminho da rota**, nunca pelo domínio, então o diff funciona com URL absoluta, relativa ou domínio antigo. A remoção checa referências (`esta_em_uso()`) antes de apagar.
- **Novo** [app/Http/Controllers/ContentImageController.php](../../../../app/Http/Controllers/ContentImageController.php) — `store()` (valida `image|mimes:png,jpg,jpeg,webp|max:5120` + `title`, grava com `Str::uuid()`, responde `201` com `{ uuid, url }`) e `show()` (`abort_unless(Str::isUuid($uuid), 404)` como defesa contra path traversal, MIME sniffado porque o arquivo não tem extensão, `Cache-Control: immutable`).
- [routes/web.php](../../../../routes/web.php) — rota pública `posts.content_image` (`GET post/content/images/{slug}/{uuid}`) e `posts.content_images.store` (`POST admin/posts/content-images`) dentro do grupo `role:autor,admin`.
- [app/Http/Controllers/PostController.php](../../../../app/Http/Controllers/PostController.php) — `update()` guarda o conteúdo anterior **antes** da atribuição e chama `sincronizar()` **depois** do `update()` (se o save falhar, nenhuma imagem é perdida); `destroy()` chama `remover_todas()` **depois** do `delete()`, para o post não contar como referência de si mesmo.
- [app/Http/Middleware/HandleInertiaRequests.php](../../../../app/Http/Middleware/HandleInertiaRequests.php) — compartilha `techpulse_url_publica` como prop do Inertia (fonte única de verdade; o frontend não tem como conhecer o domínio sozinho).

### Frontend

- [resources/js/Components/Admin/MarkdownEditor.vue](../../../../resources/js/Components/Admin/MarkdownEditor.vue) — reescrito:
  - handlers próprios de `paste`/`drop`/`dragover`/`dragleave` no CodeMirror, com `uploadImage: false`. O caminho nativo do EasyMDE decide imagem-vs-link pela **extensão da URL** (`easymde.js:881-891`), e a URL das imagens de conteúdo não tem extensão — viraria link.
  - inserção por `replaceRange` + `setCursor` (e não `replaceSelection`): no drop a posição é a do mouse, que nem sempre é onde o cursor está.
  - guarda de `defaultPrevented` no paste (o CodeMirror tem dois listeners) e `coordsChar({...}, "window")` no drop.
  - upload **sequencial** — em paralelo, a ordem das imagens no texto passaria a depender de qual requisição voltasse primeiro.
  - validação de tipo/tamanho no cliente antes de enviar, indicador de envio e mensagem de erro inline (com `status: false` o EasyMDE não mostra nada), destaque visual no arrasto e botão na toolbar que abre um seletor próprio — sem usar o nome `upload-image`, que é o item nativo do EasyMDE.
  - nova prop `titulo`, enviada para o servidor montar o slug decorativo da URL.
  - **wrapper próprio** (`div.md-editor-wrapper`): era o que faltava para o `<style scoped>` dark existente sair do papel (ver §4).
- [resources/js/helpers.js](../../../../resources/js/helpers.js) — `normalizar_origem_conteudo(texto, url_publica)`: troca a origem de produção pela do navegador nas URLs `/post/content/images/...`. Função pura, recebe a base como argumento.
- [resources/js/Pages/Post.vue](../../../../resources/js/Pages/Post.vue) — regra nova de imagem no markdown-it delegando à regra padrão e reescrevendo o `src` (feito no token, e não por substituição no texto, para não mexer dentro de blocos de código).
- [resources/js/Pages/Admin/Posts/PostsCreate.vue](../../../../resources/js/Pages/Admin/Posts/PostsCreate.vue) e [PostsEdit.vue](../../../../resources/js/Pages/Admin/Posts/PostsEdit.vue) — passam `:titulo="form.title"`.

### Testes

- **Novos** [tests/Unit/ImagensDeConteudoTest.php](../../../../tests/Unit/ImagensDeConteudoTest.php) (11 casos, extração de uuid e montagem da URL), [tests/Feature/ImagensDeConteudoTest.php](../../../../tests/Feature/ImagensDeConteudoTest.php) (11 casos, upload/serve/permissões) e [tests/Feature/LimpezaDeImagensDeConteudoTest.php](../../../../tests/Feature/LimpezaDeImagensDeConteudoTest.php) (5 casos, diff do save e `destroy`).
- Cobre explicitamente o **slug decorativo**: servir com `slug-completamente-diferente` devolve a imagem, provando que renomear o post não quebra nada.
- [phpunit.xml](../../../../phpunit.xml) — `DB_CONNECTION=sqlite` e `DB_DATABASE=:memory:` descomentados; [tests/Feature/ExampleTest.php](../../../../tests/Feature/ExampleTest.php) — `RefreshDatabase` (sem ele o teste bate no MySQL de dev).
- `composer.json` / `composer.lock` — `doctrine/dbal ^3.10` em `require-dev` (ver §4).

### Documentação

- [CONTEXT.md](../../../../CONTEXT.md) — termos **Imagem de Conteúdo** e **Imagem de Capa** em *Conteúdo* (com `imagem de destaque` marcado como termo a evitar, por colidir com o **Destaque** já definido).
- **Novo** [docs/adr/0002-imagens-de-conteudo.md](../../../adr/0002-imagens-de-conteudo.md) — slug decorativo, arquivo plano por uuid, URL absoluta + normalização de origem, limpeza por diff com checagem de referência, e as opções descartadas.
- [CLAUDE.md](../../../../CLAUDE.md) — convenção das imagens de conteúdo; **correção da afirmação de que o `content` é salvo em base64** (é base64 só no transporte; o banco guarda markdown — linhas 112 e 245 do PostController); nota sobre o estado da suíte de testes.
- [.github/agents/tech-pulse-admin.agent.md](../../../../.github/agents/tech-pulse-admin.agent.md) e [.gemini/skill/tech_pulse_admin/SKILL.md](../../../../.gemini/skill/tech_pulse_admin/SKILL.md) — atualizados **em par** (byte-idênticos a partir de `## Scope`, verificado com o `diff` documentado no próprio arquivo): o `MarkdownEditor.vue` deixa de ser "styling only", os endpoints de imagem de conteúdo entram no contrato e a mesma correção do base64 é aplicada. Sem isso, uma execução futura do agente Admin recusaria ou reverteria o backend novo.

---

## 2. Documentos Relacionados

- Survey de fatos: [20260917_imagens_conteudo_post_surveyfacts.md](../../../survey/20260917_imagens_conteudo_post_surveyfacts.md)
- Decisão arquitetural: [docs/adr/0002-imagens-de-conteudo.md](../../../adr/0002-imagens-de-conteudo.md)

---

## 3. Validação

| Comando | Resultado |
|---|---|
| `php vendor/bin/phpunit tests/...` (os 5 arquivos não-Pest) | **OK — 27 testes, 51 asserções** |
| `php vendor/bin/pint --test` | 113 arquivos, **1 problema** — `app/Models/User.php` (`class_attributes_separation`), arquivo **não tocado** nesta task e deixado como está |
| `npm run build` | **OK** (`✓ built in 20.29s`; o aviso de chunk > 500 kB é pré-existente) |
| `php artisan route:list --name=content` | `POST admin/posts/content-images` e `GET post/content/images/{slug}/{uuid}` registradas |

Verificação manual no navegador **não foi executada** — o ambiente não tem servidor de desenvolvimento nem browser. Os passos 1 a 7 da seção de verificação do plano ficam pendentes de confirmação visual, em especial o indicador de envio e o botão da toolbar (ícone e alinhamento).

> `public/build/` é versionado neste repo, então o `npm run build` renova os arquivos de build rastreados. É isso que explica o volume de arquivos `D`/`??` no `git status`, não uma alteração manual.

---

## 4. Achados e Riscos

### Corrigido nesta task

- **CSS dark do editor estava morto.** `codemirror.js fromTextArea` insere o wrapper do CodeMirror como **irmão** do `<textarea>` (não como filho), então todo seletor compilado do `<style scoped>` (`[data-v-…] .EasyMDEContainer`) nunca casava — o editor vinha no tema claro padrão do EasyMDE. Resolvido com um wrapper próprio no componente, decisão tomada pelo usuário na hora; é uma **mudança visual deliberada** (o editor passa a usar o tema "Midnight Pulse" que já estava escrito).
- **`migrate:fresh` estava quebrado no projeto inteiro.** `ChangeColumn::compile()` exige `doctrine/dbal` no Laravel 10 e a checagem é **independente do driver**, então a migration `2025_04_13_151251_add_socialite_fields_to_users_table.php:17` (`->string('password')->nullable()->change()`) derrubava qualquer migração do zero, em MySQL também. Com `doctrine/dbal ^3.10` em `require-dev` (decisão do usuário) a suíte passa a rodar em sqlite `:memory:`.
- **`content` é base64 só no transporte.** O `CLAUDE.md` afirmava que o banco guarda base64; o banco guarda markdown puro (`PostController.php:112, 245`) — afirmação corrigida no `CLAUDE.md` e nos dois gêmeos do agente Admin.

### Pendências conhecidas

- **A suíte de testes não roda inteira.** `phpunit` sem argumento morre no carregamento com `Call to undefined function test()`: `tests/Feature/Auth/*` (6 arquivos) e `tests/Feature/ProfileTest.php` estão em sintaxe do **Pest**, que não está instalado. É pré-existente e não foi tocado — os arquivos rodáveis passam individualmente.
- **`.material-symbols-outlined` nunca é definida** no CSS do projeto; o `<link>` do Google Fonts traz só o `@font-face`. O botão novo da toolbar declara a `font-family` explicitamente para ter ícone. Os demais ícones do admin dependem dessa classe e podem não renderizar como esperado — corrigir isso é uma mudança visual ampla, fora do escopo desta task, e precisa da sua decisão.
- **Órfãos de formulário abandonado**: imagem colada e apagada na mesma sessão (sem nunca ter sido salva no banco) fica no disco. Resolver exigiria comando de varredura ou tabela de registro, ambos descartados no grill.
- **Rascunho é público**: a rota que serve a imagem não tem auth, então a imagem de um post não publicado é acessível para quem tiver a URL (uuid v4, não adivinhável — mesmo comportamento da capa hoje).
