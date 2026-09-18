# CLAUDE.md

Este arquivo fornece orientações para o Claude Code trabalhar neste repositório.

## Visão Geral

O **Tech Pulse Blog** é um blog pessoal que demonstra a integração entre **Laravel (backend)**, **Inertia.js** e **Vue 3 (frontend)**. Ele também expõe uma **API pública** (formato estilo WordPress REST API) consumida por um app Flutter externo.

## Stack

- **Backend:** PHP >= 8.1, Laravel 10, Sanctum (auth de API), Socialite (login Google), spatie/laravel-markdown + shiki-php, cviebrock/eloquent-sluggable
- **Frontend:** Vue 3, Inertia.js 2, Vite 6, Tailwind CSS v3.4, markdown-it, highlight.js, mermaid, EasyMDE (editor), luxon, ziggy-js
- **Banco:** MySQL/PostgreSQL/SQLite (configurável via `.env`)

## Comandos

```bash
composer install          # Dependências PHP
npm install               # Dependências JS
php artisan serve         # Servidor de desenvolvimento (http://localhost:8000)
npm run dev               # Vite com hot reload
npm run build             # Build de produção
php artisan migrate       # Rodar migrações
php artisan db:seed       # Seeders (CreateUsersSeeder etc.)
php artisan pint          # Formatação de código PHP (laravel/pint)
phpunit                   # Testes (PHPUnit 10)
```

Ambiente: **Windows** (shell primário PowerShell).

**Testes:** rode **arquivo por arquivo** (`phpunit tests/Feature/AlgumTest.php`). Invocar `phpunit` sozinho derruba o carregamento da suíte inteira: `tests/Feature/Auth/*` e `tests/Feature/ProfileTest.php` estão em sintaxe do **Pest**, que não está instalado (`Call to undefined function test()`). O schema de teste é sqlite `:memory:` ([phpunit.xml](phpunit.xml)) e o `RefreshDatabase` depende de `doctrine/dbal` (dev).

## Arquitetura e Rotas

### Web ([routes/web.php](routes/web.php))
- `GET /` — Home (HomeController)
- `GET /login`, `POST /login`, `GET /logout` — auth custom (LoginController)
- `GET /login/google` e `/login/google/callback` — login via Google (SocialiteController)
- `GET post/show/{slug}` e `post/show/fix/{uuid}` — exibição do post (PostController; nota: ambas usam `name('posts.show')` — duplicação conhecida)
- `GET post/image/{filename}` — serve imagens (ImageController)
- `GET post/content/images/{slug}/{uuid}` — serve as imagens de conteúdo do post (ContentImageController; o `{slug}` é decorativo, ver convenções)
- `GET /register`, `POST /register` — cadastro self-service (RegisteredUserController)
- `GET /minha-conta` — dashboard do leitor (DashboardController)
- Grupo `admin` (requer auth + papel): `admin.home`/posts exigem `role:autor,admin`; categorias e `/admin/users` exigem `role:admin`

### API ([routes/api.php](routes/api.php))
- `GET /api/posts` — `PostController::list_rss` (últimos posts, `?per_page=5`)
- `GET /api/posts/pagination` — `PostController::list_published_posts` (`?page=&per_page=`, padrão 15)

## Convenções e Peculiaridades Importantes

- **Idioma:** código, comentários, mensagens de validação e UI em **português (pt-BR)**. Mantenha o padrão.
- **Conteúdo do post é base64 no transporte, não no banco:** o frontend codifica o `content` com `btoa` antes de enviar e os controllers fazem `base64_decode($request->content)` (PostController.php:112, 245), então o banco guarda **markdown puro**.
- **Imagens de capa:** salvas em `storage/app/public/images` com **nome = uuid do post, sem extensão**; exibidas via rota `post/image/{filename}` (ImageController). Ative o link simbólico com `php artisan storage:link` se necessário.
- **Imagens de conteúdo** (embutidas no markdown): salvas em `storage/app/data/post_content_images/{uuid}`, sem extensão, e servidas em `post/content/images/{slug}/{uuid}`. O `{slug}` é **decorativo** — a leitura resolve só pelo `{uuid}`, porque o slug do post muda quando o título muda. Colar ou arrastar um arquivo no editor ([MarkdownEditor.vue](resources/js/Components/Admin/MarkdownEditor.vue)) envia na hora para `POST admin/posts/content-images` e insere o markdown na posição do cursor. Os tipos/tamanho são os mesmos da capa (PNG/JPG/WebP, 5 MB). A URL gravada é **absoluta** (domínio de `config('techpulse.url_publica')`, porque o app Flutter consome a API); o frontend troca a origem pela do navegador com `normalizar_origem_conteudo()` ([helpers.js](resources/js/helpers.js)). Ao salvar, imagem que saiu do texto é **apagada do disco** — `app/Services/ImagensDeConteudo.php` faz o diff e só apaga se nenhum outro post ainda referenciar o uuid. Ver [docs/adr/0002-imagens-de-conteudo.md](docs/adr/0002-imagens-de-conteudo.md).
- **Slugs:** gerados pelo helper global `criar_slug()` (app/helpers.php, autoload via composer) e garantidos únicos no `save()` sobrescrito do modelo `Post` (adiciona sufixo `-2`, `-3`...).
- **IDs de API:** a API expõe `uuid` (não o `id` numérico) como identificador público dos posts; links externos usam o domínio de produção **hardcoded** `https://tech-pulse.natanfiuza.dev.br/` nas respostas de API.
- **Auth e perfis:** registro self-service em `/register` (novos usuários nascem como `leitor`). Papéis: `leitor`, `autor`, `admin` (coluna `role`). Autor vê/edita/exclui apenas os próprios posts; admin vê tudo e gerencia usuários em `/admin/users` (promover/diminuir, excluir com **soft delete** — conteúdo preservado, exibido como "Usuário removido"). `CreateUsersSeeder` cria os admins iniciais. Redirect pós-login/cadastro pelo helper `caminho_inicial_do_usuario()` (leitor → `/minha-conta`; autor/admin → `/admin/home`).
- **CORS:** configurado para permitir o consumo da API pelo app Flutter (ver [config/cors.php](config/cors.php)).
- **Rotas mal definidas (não "consertar" sem autorização):** `Route::get('post/show/fix/{uuid}', ...)` reutiliza `name('posts.show')`, e `destroy` do PostController chama `update` em vez do método destrutivo (provavelmente bug histórico, mas altere só se solicitado).
- Queries usam `whereRaw`/`orderByRaw` com interpolação direta em alguns pontos do PostController — ao editar esses métodos, prefira query builder/Eloquent com bindings.

## Frontend

- Páginas principais: [resources/js/Pages/](resources/js/Pages/) — `Home.vue`, `Post.vue`, `Auth/Login.vue`, `Auth/Register.vue`, `Reader/Dashboard.vue`, além das páginas `Admin/` (inclui `Admin/Users.vue`).
- **Design system:** os protótipos da versão 3 ("Midnight Pulse") em [docs/prototipos/versao_3/](docs/prototipos/versao_3/) são a fonte de verdade para mudanças visuais (telas: `DESIGN.md`, `code.html`, `screen.png`). Para mudanças de layout público, use o agente **TechPulse Layout** ([.github/agents/tech-pulse-layout.agent.md](.github/agents/tech-pulse-layout.agent.md)); para o admin, use o agente **TechPulse Admin** ([.github/agents/tech-pulse-admin.agent.md](.github/agents/tech-pulse-admin.agent.md)).
- Documentação de apoio em [docs/](docs/) (bibliotecas, estrutura de categorias, protótipos).

## Modelos de Dados

- **Post:** `id`, `user_id`, `uuid`, `title`, `slug` (único), `image` (path da capa), `excerpt`, `content` (longText, markdown puro)
- **Category:** `id`, `name`, `slug` (único), `description`, `scope`, `possible_contents`, `post_suggestions`, `parent_id` (recursivo, nullable)
- **User:** `role` (`leitor`/`autor`/`admin`), campos do Socialite (Google), soft delete (`deleted_at`)
- **PostView:** histórico de visualizações por usuário logado (`user_id`, `post_id`, `viewed_at`; único por par)

## Code Conventions

- **Proibido**: `console.log` (JS), `dd()` (PHP), `localhost`/`127.0.0.1` in URLs
- **Rotas RESTful**: avoid verbs (`buscar`, `listar`) in paths
- **DocBlocks** expected on PHP functions (warning)
- **TODO/FIXME** flagged as warning
- **snake_case**: All JavaScript/Vue function names and variable names must use `snake_case` (e.g., `change_cliente`, `selected_cliente`, `session_uuid`). No `camelCase`. PHP already follows this convention.

## Rules

- After completing any task, generate report at `docs/claude-code/reports/{branch}/{YYYYMMDD}_{taskname}.md`
- When referencing files inside generated reports, use relative paths from the report directory (e.g. `../../../../.agents/skills/karpathy-guidelines/SKILL.md`) instead of absolute `file:///` URLs.

## Agent skills

### Issue tracker

Issues e specs ficam como arquivos markdown em `.scratch/<feature-slug>/`. Veja `docs/agents/issue-tracker.md`.

### Domain docs

Layout single-context: um `CONTEXT.md` + `docs/adr/` na raiz do repo. Veja `docs/agents/domain.md`.
