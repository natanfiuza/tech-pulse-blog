# CLAUDE.md

Este arquivo fornece orientações para o Claude Code trabalhar neste repositório.

## Visão Geral

O **Tech Pulse Blog** é um blog pessoal que demonstra a integração entre **Laravel (backend)**, **Inertia.js** e **Vue 3 (frontend)**. Ele também expõe uma **API pública** (formato estilo WordPress REST API) consumida por um app Flutter externo.

## Stack

- **Backend:** PHP >= 8.1, Laravel 10, Sanctum (auth de API), Socialite (login Google), spatie/laravel-markdown + shiki-php, cviebrock/eloquent-sluggable
- **Frontend:** Vue 3, Inertia.js 2, Vite 6, Bootstrap 5, markdown-it, highlight.js, mermaid, EasyMDE (editor), luxon, ziggy-js
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

## Arquitetura e Rotas

### Web ([routes/web.php](routes/web.php))
- `GET /` — Home (HomeController)
- `GET /login`, `POST /login`, `GET /logout` — auth custom (LoginController)
- `GET /login/google` e `/login/google/callback` — login via Google (SocialiteController)
- `GET post/show/{slug}` e `post/show/fix/{uuid}` — exibição do post (PostController; nota: ambas usam `name('posts.show')` — duplicação conhecida)
- `GET post/image/{filename}` — serve imagens (ImageController)
- Grupo `admin` (requer auth): CRUD de posts e categorias

### API ([routes/api.php](routes/api.php))
- `GET /api/posts` — `PostController::list_rss` (últimos posts, `?per_page=5`)
- `GET /api/posts/pagination` — `PostController::list_published_posts` (`?page=&per_page=`, padrão 15)

## Convenções e Peculiaridades Importantes

- **Idioma:** código, comentários, mensagens de validação e UI em **português (pt-BR)**. Mantenha o padrão.
- **Conteúdo do post é base64:** o campo `content` é salvo no banco já codificado em base64; os controllers fazem `base64_decode($request->content)` no store/update (PostController.php:82, 147).
- **Imagens:** salvas em `storage/app/public/images` com **nome = uuid do post, sem extensão**; exibidas via rota `post/image/{filename}` (ImageController). Ative o link simbólico com `php artisan storage:link` se necessário.
- **Slugs:** gerados pelo helper global `criar_slug()` (app/helpers.php, autoload via composer) e garantidos únicos no `save()` sobrescrito do modelo `Post` (adiciona sufixo `-2`, `-3`...).
- **IDs de API:** a API expõe `uuid` (não o `id` numérico) como identificador público dos posts; links externos usam o domínio de produção **hardcoded** `https://tech-pulse.natanfiuza.dev.br/` nas respostas de API.
- **Auth:** não há registro self-service — usuários são criados via seed/tinker; `CreateUsersSeeder` é o seeder de referência.
- **CORS:** configurado para permitir o consumo da API pelo app Flutter (ver [config/cors.php](config/cors.php)).
- **Rotas mal definidas (não "consertar" sem autorização):** `Route::get('post/show/fix/{uuid}', ...)` reutiliza `name('posts.show')`, e `destroy` do PostController chama `update` em vez do método destrutivo (provavelmente bug histórico, mas altere só se solicitado).
- Queries usam `whereRaw`/`orderByRaw` com interpolação direta em alguns pontos do PostController — ao editar esses métodos, prefira query builder/Eloquent com bindings.

## Frontend

- Páginas principais: [resources/js/Pages/](resources/js/Pages/) — `Home.vue`, `Post.vue`, `PostMermaid.vue`, além das páginas `Admin/`.
- **Design system:** os protótipos da versão 3 ("Midnight Pulse") em [doc/prototipos/versao_3/](doc/prototipos/versao_3/) são a fonte de verdade para mudanças visuais (telas: `DESIGN.md`, `code.html`, `screen.png`). Para mudanças de layout, use o agente **TechPulse Layout** ([.github/agents/tech-pulse-layout.agent.md](.github/agents/tech-pulse-layout.agent.md)).
- Documentação de apoio em [doc/](doc/) (bibliotecas, estrutura de categorias, protótipos).

## Modelos de Dados

- **Post:** `id`, `user_id`, `uuid`, `title`, `slug` (único), `image` (path), `excerpt`, `content` (longText, base64)
- **Category:** `id`, `name`, `slug` (único), `description`, `scope`, `possible_contents`, `post_suggestions`, `parent_id` (recursivo, nullable)
- **User:** inclui campos do Socialite (Google) — `add_socialite_fields_to_users_table` migration

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
