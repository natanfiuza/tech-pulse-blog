# Survey de Fatos — Imagens de Conteúdo no Editor de Posts

**Data:** 17 de setembro de 2026
**Contexto:** Cadastro/edição de posts no painel administrativo (`Admin/Posts/PostsCreate.vue`, `PostsEdit.vue` e o editor `Components/Admin/MarkdownEditor.vue`).
**Pedido original:** receber imagens como no editor de issues do GitHub — colar ou arrastar um arquivo faz upload imediato, o arquivo recebe um uuid e vai para `storage/app/data/post_content_images`, acessível em `/post/content/images/{slug_do_titulo}/{uuid}`; imagem que saiu do texto é excluída fisicamente ao salvar; a imagem entra **onde o cursor estiver**.

---

## 1. Problema e Motivação

- O editor (EasyMDE) estava sem `uploadImage`: colar ou arrastar um arquivo não fazia nada. Para mostrar uma captura de tela, o autor precisava hospedar a imagem fora e colar o link na mão.
- O pedido embute o **slug do título** na URL da imagem, mas `Post::save()` regenera o slug sempre que o título muda (`app/Models/Post.php:156`). Renomear um post quebraria todas as imagens já gravadas nele, sem que o markdown no banco fosse reescrito.

---

## 2. Decisões (rodadas do grill)

### Rodada 1

| # | Decisão | Por quê |
|---|---|---|
| 1 | **Slug decorativo**: a rota resolve o arquivo **só pelo `{uuid}`** e ignora o `{slug}` | Renomear o título nunca quebra imagem; nenhum markdown precisa ser reescrito |
| 2 | Arquivos **planos por uuid de imagem**, sem extensão | Mesma convenção da capa (`storage/app/public/images/{uuid}`); evita pasta temporária no `create`, quando o post ainda não existe |
| 3 | **Upload imediato** no paste/drop | É o comportamento do GitHub. Órfãos de formulário abandonado são aceitos (ver §4) |
| 4 | **Limpeza por diff do markdown no save** | Sem tabela nova, sem migration; a decisão fica 100% no servidor |

### Rodada 2

| # | Decisão | Por quê |
|---|---|---|
| 5 | **URL absoluta gravada no markdown**, com normalização de origem no frontend | O app Flutter consome a API e precisa de URL absoluta; o front troca o domínio pelo `window.location.origin` |
| 6 | Prefixo absoluto vem de **config novo**, com default de produção | `config('app.url')` é `http://localhost:8000` no `.env` local — gravaria localhost no banco |
| 7 | **Handlers próprios no CodeMirror**, com `uploadImage: false` | O caminho nativo do EasyMDE escolhe imagem-vs-link pela extensão da URL (`easymde.js:881-891`) — URL sem extensão viraria **link**; além disso ele perde todo feedback com `status: false`, duplica conteúdo no paste e quebra o arrastar de texto interno |
| 8 | **Checar referências antes de apagar** | Se outro post ainda referencia o uuid, o arquivo fica |
| 9 | **`destroy` apaga as imagens de conteúdo** do post | A capa fica fora desta task |

### Rodada 3

| # | Decisão | Por quê |
|---|---|---|
| 10 | Tipos/tamanho **iguais aos da capa**: PNG, JPG, JPEG, WebP, 5 MB | Mesma regra de `PostController.php:75` |
| 11 | **Testes + sqlite `:memory:`** no `phpunit.xml` | Sem isso o `RefreshDatabase` roda contra o MySQL real do `.env` e apaga o banco de dev |
| 12 | **Indicador de upload + botão de upload na toolbar** | Com `status: false` o EasyMDE não mostra nada, nem erro |
| 13 | Documentar em `CONTEXT.md`, `docs/adr/0002`, `CLAUDE.md` e nos **dois gêmeos** do agente Admin | Uma execução futura do agente Admin recusaria ou reverteria o backend novo |

---

## 3. Relatório de Fatos Levantados

### Ambiente

- PHP **8.3.19**; extensões `gd=1`, `imagick=1`, `fileinfo=1`; `Str::isUuid` existe (Laravel 10).
- `local` disk root = `storage_path('app')`; `FILESYSTEM_DISK=local`. O symlink `public/storage` está quebrado — é por isso que as imagens são servidas por controller, não pelo servidor web.
- MySQL é o banco do `.env` (`tech_pulse` em `127.0.0.1`); não existe `.env.testing`.

### Editor e CodeMirror

- `signalDOMEvent` chama `signal(cm, type, cm, e)` **antes** de checar `defaultPrevented`. Consequência prática: um handler próprio com `preventDefault()` roda primeiro e impede o `onDrop`/`onPaste` interno do CodeMirror de inserir o conteúdo cru.
- O CodeMirror tem **dois** listeners de `paste` (textarea e scroller) — sem a guarda de `defaultPrevented` o upload sairia duplicado.
- `cm.coordsChar()` interpreta as coordenadas como **página** quando o modo é omitido; para usar `clientX/clientY` é obrigatório passar `"window"`.
- O CSS `<style scoped>` do `MarkdownEditor.vue` estava **morto**: `fromTextArea` insere o wrapper do CodeMirror como **irmão** do textarea (não como filho), então o seletor compilado `[data-v-…] .EasyMDEContainer` nunca casava e o editor ficava no tema claro padrão do EasyMDE. Corrigido com um wrapper próprio no componente (decisão do usuário), o que ativou todo o bloco dark que já existia.
- A classe `.material-symbols-outlined` **nunca é definida** no CSS do projeto — o `<link>` do Google Fonts fornece apenas o `@font-face`. O botão novo da toolbar declara a `font-family` explicitamente para ter ícone visível. Corrigir isso para todos os ícones do projeto é uma mudança visual ampla, fora do escopo.

### Ziggy

- `@routes` emite `const Ziggy = {...}` — um binding **lexical**, não `window.Ziggy`. O merge em `resources/js/ziggy.js` nunca dispara; o `route()` global usa a tabela injetada pelo **servidor**, que já inclui as rotas novas.
- **Não rodar `ziggy:generate`**: ele sobrescreve `resources/js/ziggy.js` e apaga o patch de `window.location.origin` do commit `b8bd3c4`.
- Padrão validado em `PostsCreate.vue:215`: chamar o `route()` global **dentro de um método**.

### Conteúdo do post

- O `content` é base64 **só no transporte**: o frontend codifica com `btoa` e o backend faz `base64_decode` (`PostController.php:112` no store, `:245` no update). **O banco guarda markdown puro** — a afirmação anterior do `CLAUDE.md` estava incorreta e foi corrigida.

### Testes

- **Pest não está instalado.** `tests/Feature/Auth/*` (6 arquivos) e `tests/Feature/ProfileTest.php` usam sintaxe do Pest e derrubam o carregamento da suíte inteira com `Call to undefined function test()`. Rodar `phpunit` sem argumento não executa nada.
- `->change()` no Laravel 10 exige **`doctrine/dbal`**: `ChangeColumn::compile()` checa `isDoctrineAvailable()` e lança exceção **independente do driver**. A migration `2025_04_13_151251_add_socialite_fields_to_users_table.php:17` usa `->string('password')->nullable()->change()`, então `migrate:fresh` estava quebrado em **qualquer** banco — e sem migrations não há `RefreshDatabase`. Instalado `doctrine/dbal ^3.10` como dependência **de dev** (decisão do usuário).
- Não existe `PostFactory`; `user_id` **não** está no `$fillable` de `Post` (criar post em teste exige instanciar e atribuir). `role` **é** fillable em `User`.
- `Storage::fake('local')` intercepta o disco porque o código usa `Storage::disk('local')` explícito, e não `storage_path()` cru.

### Helper de slug

- `criar_slug('')` devolve `''` → a URL sairia com slug vazio e não casaria com a rota, deixando a imagem inacessível para sempre. Daí o fallback `sem-titulo`.
- Título formado só por stopwords cai no passo 6 do helper (`Str::slug` do original), então o fallback só é acionado por título vazio ou só de pontuação.

---

## 4. Limitações aceitas

- **Órfãos de formulário abandonado**: upload imediato + limpeza por diff no save ⇒ uma imagem colada e apagada **na mesma sessão** (nunca chegou ao banco) fica no disco sem referência. Resolver exigiria comando de varredura ou tabela de registro — ambos descartados.
- **Imagem de capa**: a troca/exclusão da capa continua deixando arquivo órfão (comportamento atual, fora do escopo).
- **Domínio duplicado**: `image_front_url` continua com a string de produção hardcoded em `PostController.php:388` e `:432`.
- **Imagem de rascunho é pública**: a rota de servir não tem auth (o conteúdo do blog é público), então a imagem de um post não publicado fica acessível para quem tiver a URL. O uuid v4 não é adivinhável, e é o mesmo comportamento da capa hoje.
