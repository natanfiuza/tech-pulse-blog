# Relatório — Perfis de usuário e tela de cadastro (2026-08-31)

Branches: `develop_natan` (base `main`)

## Contexto

Via skill de grilling (`/mattpocock-skills:grill-with-docs`), o usuário pediu correções na tela principal
(links de categoria com `localhost:8000`, resumo do destaque ilegível, botão "Cadastre-se" inerte) e, em
rodadas subsequentes, definiu o modelo de **perfis de usuário** (leitor/autor/admin), **cadastro
self-service**, **gestão de usuários no admin** (soft delete) e **dashboard do leitor**. Spec aprovada e
salva em [../../../../.scratch/perfis-usuario-cadastro/spec.md](../../../../.scratch/perfis-usuario-cadastro/spec.md).

## Implementado

### Correções da home (itens 1–3)

| Item | O quê | Onde |
|---|---|---|
| 1 | Links de categoria apontando para `localhost:8000` | [../../../../resources/js/ziggy.js](../../../../resources/js/ziggy.js) — o arquivo gerado tinha URL local hardcoded; agora usa a URL base servida pelo servidor (`window.Ziggy`, que vem de `config('app.url')` via `HandleInertiaRequests`) |
| 2 | Resumo do destaque com cor opaca | [../../../../resources/js/Pages/Home.vue](../../../../resources/js/Pages/Home.vue) — texto branco com sombra (`text-white` + `text-shadow`) |
| 3 | Botão "Cadastre-se" sem destino | Rota `GET/POST /register` ([../../../../app/Http/Controllers/Auth/RegisteredUserController.php](../../../../app/Http/Controllers/Auth/RegisteredUserController.php)) + [../../../../resources/js/Pages/Auth/Register.vue](../../../../resources/js/Pages/Auth/Register.vue) (espelho do Login, Midnight Pulse, botão "Entrar com Google") + link no [../../../../resources/js/Pages/Auth/Login.vue](../../../../resources/js/Pages/Auth/Login.vue) |

### Perfis de usuário (itens 4–7)

- **Modelo:** coluna `role` (`leitor`/`autor`/`admin`) com backfill de todos os usuários existentes como `admin`
  (migração [../../../../database/migrations/2026_08_31_000001_add_role_to_users_table.php](../../../../database/migrations/2026_08_31_000001_add_role_to_users_table.php));
  constantes `ROLE_*` e métodos `possui_papel()`/`posts()` em [../../../../app/Models/User.php](../../../../app/Models/User.php).
- **Middleware** [../../../../app/Http/Middleware/EnsureUserHasRole.php](../../../../app/Http/Middleware/EnsureUserHasRole.php) registrado como alias `role`.
  Rotas ([../../../../routes/web.php](../../../../routes/web.php)): posts/admin exigem `role:autor,admin`; categorias e `/admin/users` exigem `role:admin`.
- **Autor:** vê/edita/exclui apenas posts próprios (`PostController` — `autorizar_post()`), admin vê tudo.
- **Redirect pós-login/cadastro** por papel via helper [../../../../app/helpers.php](../../../../app/helpers.php)
  (`caminho_inicial_do_usuario()`: leitor → `/minha-conta`, autor/admin → `/admin/home`).
- **Gestão de usuários** [../../../../app/Http/Controllers/UserController.php](../../../../app/Http/Controllers/UserController.php) +
  [../../../../resources/js/Pages/Admin/Users.vue](../../../../resources/js/Pages/Admin/Users.vue): promover/diminuir,
  **excluir com soft delete** (migração `deleted_at`); guardrails: não gerencia a si mesmo nem outro admin.
  Conteúdo preservado — exibido como **"Usuário removido"** sem foto em
  [../../../../resources/js/Components/CommentThread.vue](../../../../resources/js/Components/CommentThread.vue),
  [../../../../resources/js/Components/PostCard.vue](../../../../resources/js/Components/PostCard.vue) e
  [../../../../resources/js/Pages/Admin/Posts/PostsIndex.vue](../../../../resources/js/Pages/Admin/Posts/PostsIndex.vue).
- **Dashboard do leitor** `/minha-conta` ([../../../../app/Http/Controllers/DashboardController.php](../../../../app/Http/Controllers/DashboardController.php) +
  [../../../../resources/js/Pages/Reader/Dashboard.vue](../../../../resources/js/Pages/Reader/Dashboard.vue)):
  histórico de visualizações (tabela `post_views`, migração [../../../../database/migrations/2026_08_31_000003_create_post_views_table.php](../../../../database/migrations/2026_08_31_000003_create_post_views_table.php),
  gravado no `PostController::show` quando logado), comentários e perfil.
- **Autor nas listagens:** `post.user` (nome) nos cards públicos e na listagem do admin; Navbar ajustada por papel.

## Bugs encontrados e corrigidos

- **Relação `user()` ausente no modelo `Post`** ([../../../../app/Models/Post.php](../../../../app/Models/Post.php)):
  a tabela `posts` tem `user_id` e o front já consumia `post.user`, mas a relação BelongsTo nunca existiu —
  qualquer `->with('user')` estourava `RelationNotFoundException`. Pré-existente; a feature o expôs. **Adicionada.**
  Sem essa correção a home daria 500 em produção.
- **CSRF no smoke test**: o teste inicial enviava o token com `%3D` (valor do cookie); o browser envia o valor
  URL-decodificado. Não é bug do app — o fluxo do browser (axios/Inertia) funciona.

## Desvios de escopo (decisões tomadas na implementação)

1. **Categorias restritas a `role:admin`** (o spec dizia autor+admin): categorias são estrutura compartilhada
   sem dono; permitir que autores editem a taxonomia afetaria o blog inteiro. Recomendo manter admin-only,
   mas está flagrado para decisão do usuário.
2. **Rastreio de visualização só para usuários logados** (conforme grilling, sem cookie para anônimos).

## Validação executada

- `php artisan migrate` — 3 migrações OK, backfill: 3 usuários existentes → `admin`.
- `npm run build` — OK (chunks `Register`, `Dashboard`, `Users` gerados). `public/build` está rastreado pelo git — o commit levará a tela nova.
- Smoke test HTTP (`artisan serve` + curl): `GET /`, `/login`, `/register` → 200; `/admin/users` e `/minha-conta` sem login → 302 `/login`.
- `POST /register` (CSRF + JSON) → 302 `/minha-conta`: criação com papel `leitor`, login automático e redirect por papel OK (usuário de teste criado e removido em seguida).
- `POST /login` (leitor) → 302 `/minha-conta`; `/minha-conta` autenticado → 200.
- **Pendência de verificação manual:** autor restrito aos próprios posts e admin vendo todos; exclusão de usuário com comentários (soft delete → "Usuário removido"); `Admin/Users` (promover/diminuir).

## Notas de deploy

1. **Rodar as migrações em produção** (`php artisan migrate --force`) — cria `role`, `deleted_at` e `post_views`, e promove os usuários existentes a `admin`.
2. **`APP_URL` de produção deve ser o domínio real** (`https://tech-pulse.natanfiuza.dev.br/`): o fix do localhost depende de `config('app.url')`; o ziggy gerado agora é sobrescrito pela URL do servidor.
3. **`npm run build` antes de subir** — `public/build` regenerado com as novas páginas.

## Arquivos criados

- [../../../../.scratch/perfis-usuario-cadastro/spec.md](../../../../.scratch/perfis-usuario-cadastro/spec.md)
- [../../../../app/Http/Middleware/EnsureUserHasRole.php](../../../../app/Http/Middleware/EnsureUserHasRole.php)
- [../../../../app/Http/Controllers/UserController.php](../../../../app/Http/Controllers/UserController.php)
- [../../../../app/Http/Controllers/DashboardController.php](../../../../app/Http/Controllers/DashboardController.php)
- [../../../../app/Models/PostView.php](../../../../app/Models/PostView.php)
- [../../../../resources/js/Pages/Auth/Register.vue](../../../../resources/js/Pages/Auth/Register.vue)
- [../../../../resources/js/Pages/Reader/Dashboard.vue](../../../../resources/js/Pages/Reader/Dashboard.vue)
- [../../../../resources/js/Pages/Admin/Users.vue](../../../../resources/js/Pages/Admin/Users.vue)
- Migrações: `2026_08_31_000001_add_role_to_users_table.php`, `2026_08_31_000002_add_deleted_at_to_users_table.php`, `2026_08_31_000003_create_post_views_table.php`

## Arquivos alterados

`CLAUDE.md`, `CONTEXT.md` (glossário: Usuário, Leitor, Autor, Admin, Visualização), `routes/web.php`,
`app/Models/User.php`, `app/Models/Post.php`, `app/helpers.php`, `app/Http/Kernel.php` (alias `role`),
`app/Http/Middleware/HandleInertiaRequests.php` (role no `auth.user`), `app/Http/Controllers/PostController.php`,
`app/Http/Controllers/HomeController.php`, `app/Http/Controllers/Auth/LoginController.php`,
`app/Http/Controllers/Auth/SocialiteController.php`, `app/Http/Controllers/Auth/RegisteredUserController.php`,
`resources/js/ziggy.js`, `resources/js/Pages/Home.vue`, `resources/js/Pages/Auth/Login.vue`,
`resources/js/Components/Navbar.vue`, `resources/js/Components/PostCard.vue`,
`resources/js/Components/CommentThread.vue`, `resources/js/Pages/Admin/Posts/PostsIndex.vue`,
`resources/js/Layouts/AdminLayout.vue`, `public/build/*` (regenerado).

## Próximos passos

- Commit via gitpr (não realizado — aguardando pedido explícito).
- Verificação manual listada em "Pendência" e decisão sobre o desvio das categorias.
