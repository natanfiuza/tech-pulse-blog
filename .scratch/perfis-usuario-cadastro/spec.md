# Spec: Perfis de usuário, cadastro e correções da home

Status: aprovada (2026-08-31)

## Contexto

O blog migrou para o design "Midnight Pulse" v3 (Tailwind, sem Bootstrap) e a nova tela de login foi testada em produção com sucesso. A partir desse teste, foram levantadas três correções na tela principal (home) e uma evolução do modelo de auth: hoje todo usuário logado tem acesso total ao admin, e não há registro self-service (usuários criados via seed).

## Objetivo

1. Corrigir links de categoria que apontam para `localhost:8000` em produção.
2. Melhorar a legibilidade do resumo da notícia em destaque (branco com sombra).
3. Criar tela de cadastro self-service com opção de login/cadastro via Google.
4. Introduzir perfis de usuário (leitor/comentarista, autor, admin) com regras de visibilidade de posts, gestão de usuários no admin (com soft delete) e dashboard para leitores.

## Decisões (rodadas de grilling)

- **Perfis**: coluna `role` no `User` — `leitor` (padrão), `autor`, `admin`.
- **Backfill**: usuários existentes (seeded) recebem `admin` na migração, para o blog não perder acesso.
- **Regras de posts**: autor vê/edita/exclui **apenas os próprios posts**; admin vê/edita/exclui todos.
- **Acesso admin**: `/admin/*` → `autor` + `admin`; `/admin/users` → somente `admin`.
- **Gestão de usuários** (`/admin/users`): listar usuários, promover/diminuir (leitor ⇄ autor ⇄ admin), excluir com **soft delete** (sem cascata). Guardrails: ninguém altera/exclui a si mesmo; admin não rebaixa/exclui outro admin.
- **Usuário removido**: comentários e posts de usuário soft-deletado exibem "Usuário removido" sem foto; o conteúdo continua público.
- **Dashboard do leitor** (`/minha-conta`): histórico de visualizações (nova tabela `post_views`, apenas usuário logado), meus comentários, perfil.
- **Redirect pós-login/cadastro por papel**: leitor → `/minha-conta`; autor/admin → `/admin/home`. Cadastro faz auto-login.
- **Cadastro**: `name`, `email`, `password` + confirmação; botão Google reutiliza a rota existente `/login/google` (que já cria a conta se não existir). Link "Cadastre-se" apenas na tela de login.
- **Autor nas listagens**: coluna autor na listagem do admin **e** nos cards públicos da home.
- **Correção localhost**: frontend passa a usar `url`/`port` do `window.Ziggy` compartilhado pelo servidor (que lê `config('app.url')`), com fallback local. Nota de deploy: `APP_URL` em produção deve ser o domínio real.

## Escopo

### 1. Links de categoria — `localhost:8000`

- `resources/js/ziggy.js` e `resources/js/app.js`: usar `url`/`port` do `window.Ziggy` quando disponível; manter fallback local para dev.
- Verificação: clicar em categoria na home e no navbar gera URL do domínio de produção, não `localhost:8000`.

### 2. Resumo da notícia em destaque

- `resources/js/Pages/Home.vue` (seção `featured_post`): resumo em **branco** (`#fff`) com `text-shadow` suave, mantendo o overlay escuro da imagem.

### 3. Tela de cadastro

- Rotas `GET/POST /register` em `routes/web.php` → `RegisteredUserController` (já existe, completo).
- Nova página `resources/js/Pages/Auth/Register.vue` seguindo o design Midnight Pulse (mesmo padrão visual do Login), com botão Google.
- `RegisteredUserController::store` redireciona por papel (leitor → `/minha-conta`).

### 4. Perfis e permissões

- Migração: coluna `role` (string, default `leitor`) + backfill dos existentes → `admin`.
- Novo middleware `EnsureUserHasRole` (`role:autor,admin` / `role:admin`), registrado no kernel e aplicado aos grupos admin.
- `PostController`: listagem filtra por papel (autor → `user_id = auth`); `store` restrito a autor/admin; `update`/`destroy` apenas do dono ou admin; `show` registra visualização quando logado.
- `LoginController` e `SocialiteController`: redirect por papel (helper comum).

### 5. Gestão de usuários no admin

- Migração: `deleted_at` no `users` (SoftDeletes).
- Novo `UserController` (index/update/destroy com soft delete) + rotas `/admin/users` (admin).
- Nova página `resources/js/Pages/Admin/Users.vue`: tabela (nome, email, papel, ações).
- Autor na listagem do admin (`PostsIndex`) e nos cards públicos (`PostCard`); usuário removido → "Usuário removido".

### 6. Dashboard do leitor

- Migração: tabela `post_views` (`user_id`, `post_id`, `viewed_at`, único por par).
- Novo `DashboardController` + `resources/js/Pages/Reader/Dashboard.vue`: histórico de visualizações, meus comentários, perfil.
- `Navbar`: leitor → `/minha-conta`; autor/admin → `/admin/home`.

### 7. Usuário removido nos comentários

- `CommentSection.vue`: comentário de usuário soft-deletado exibe "Usuário removido", sem foto.

### 8. Documentação e fechamento

- Atualizar `CLAUDE.md` (auth agora tem registro self-service) e `CONTEXT.md` (glossário: Usuário / Leitor / Autor / Admin).
- `npm run build` e teste local antes do commit/deploy do usuário.
- Relatório final em `docs/claude-code/reports/{branch}/{YYYYMMDD}_perfis-usuario-cadastro.md`.

## Fora de escopo

- "Esqueceu sua senha?" (continua inerte; rotas de reset não carregadas).
- Botões GitHub/LinkedIn (links externos decorativos, sem OAuth).
- Campo `remember` morto no Login.
- Deleção em cascata de dados de usuário.

## Critérios de aceite

- Nenhuma URL com `localhost:8000` em produção (categorias/home/navbar).
- Resumo do destaque legível (branco com sombra).
- Cadastro cria usuário `leitor`, loga e redireciona para `/minha-conta`; Google idem.
- Autor só vê/edita/exclui os próprios posts; admin vê todos; leitor sem acesso a `/admin/*`.
- Admin gerencia usuários (promover/diminuir/excluir com soft delete) sem lockout (não altera a si mesmo nem rebaixa outro admin).
- Comentários/posts de usuário removido exibem "Usuário removido" sem foto.
- Dashboard do leitor mostra histórico de visualizações, comentários e perfil.
- `npm run build` sem erros; teste manual do fluxo completo.

## Arquivos afetados

- **Frontend**: `resources/js/app.js`, `resources/js/ziggy.js`, `Pages/Home.vue`, `Pages/Auth/Login.vue` (link), `Pages/Auth/Register.vue` (novo), `Pages/Admin/Users.vue` (novo), `Pages/Admin/PostsIndex.vue`, `Pages/Reader/Dashboard.vue` (novo), `Components/Navbar.vue`, `Components/PostCard.vue`, `Components/CommentSection.vue`
- **Backend**: `routes/web.php`, `app/Http/Kernel.php`, `app/Http/Middleware/EnsureUserHasRole.php` (novo), `app/Http/Controllers/Auth/RegisteredUserController.php`, `Auth/LoginController.php`, `Auth/SocialiteController.php`, `app/Http/Controllers/PostController.php`, `app/Http/Controllers/AdminController.php`, `app/Http/Controllers/UserController.php` (novo), `app/Http/Controllers/DashboardController.php` (novo), `app/Models/User.php`, `app/Models/Post.php`, `app/Models/Comment.php`, `app/Providers/RouteServiceProvider.php`
- **Banco**: migrações `add_role_to_users`, `add_deleted_at_to_users`, `create_post_views`
- **Docs**: `CLAUDE.md`, `CONTEXT.md`, relatório em `docs/claude-code/reports/`
