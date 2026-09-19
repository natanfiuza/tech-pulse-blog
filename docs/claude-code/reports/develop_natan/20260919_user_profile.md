# Relatório: Tela de Perfil do Usuário, Avatares, Perfil Público e Layout

- **Data:** 19/09/2026
- **Branch:** `develop_natan`
- **Tarefa:** Implementação da tela de perfil de usuário com layout master-details, avatar padrão com iniciais em fundo pastel, perfil público paginado (`/{username}`), caixa de autor nos artigos e correções de layout.

---

## 1. Resumo das Mudanças

### Backend
- **Migrações:**
  - `database/migrations/2026_09_19_000001_add_uuid_to_users_table.php`: adicionou a coluna `uuid` única indexada à tabela `users` e preencheu usuários já cadastrados.
  - `database/migrations/2026_09_19_000002_create_user_profiles_table.php`: criou a tabela `user_profiles` com `username`, `bio`, `social_links` em JSON e flags de privacidade (`public_profile_enabled`, `show_email`, `show_name`, `show_author_box`).
- **Modelos:**
  - `app/Models/UserProfile.php`: novo modelo com relacionamento para `User`.
  - `app/Models/User.php`: atualizado com `booted()` para geração de UUID e perfil inicial, relacionamento `profile()`, método `obter_ou_criar_perfil()` e accessor `avatar_url`.
  - `app/Models/Post.php`: inclusão de `user_id` no `$fillable` e geração automática de `uuid` no método `save()`.
- **Serviços:**
  - `app/Services/AvatarService.php`: gerador de SVG com iniciais e paleta pastel com letras pretas (`#1A1A1A`), persistência de upload e fallback de imagens em `storage/app/data/user_profile_images/{uuid}`.
- **Controllers & Rotas:**
  - `app/Http/Controllers/UserProfileController.php`: formulários de edição master-details para admin (`/admin/perfil`) e leitor (`/minha-conta/perfil`), upload de imagem, reset de avatar e validação de nomes de usuário reservados.
  - `app/Http/Controllers/UserProfileImageController.php`: endpoint público `GET /user/avatar/{uuid}` para entrega segura e com cache das fotos de perfil.
  - `app/Http/Controllers/PublicProfileController.php`: rota pública `GET /{username}` com dados autorizados do autor e listagem paginada de 10 artigos.
  - `app/Http/Controllers/PostController.php`: inclusão do eager-loading de `user.profile` para disponibilidade nos posts.
  - `routes/web.php`: registro das novas rotas de perfil e endpoint de avatar.

### Frontend
- **Componentes:**
  - `resources/js/Components/Profile/ProfileMasterDetails.vue`: painel master-details com abas verticais (Identidade & Foto, Redes Sociais, Privacidade), preview instantâneo de avatar, alternador de visibilidade social e botão verde de destaque (`Salvar Alterações`).
  - `resources/js/Components/AuthorBox.vue`: card dinâmico no rodapé dos posts com bio, foto e redes autorizadas.
  - `resources/js/Components/Admin/Sidebar.vue`: foto de perfil exibida ao lado da opção "Perfil", link para `/admin/perfil` e status ativo.
  - `resources/js/Components/Admin/Topbar.vue`: remoção do elemento vazio/placeholder redundante no canto superior direito.
  - `resources/js/Components/Navbar.vue`: exibição do avatar dinâmico no menu de conta.
- **Páginas:**
  - `resources/js/Pages/Admin/Profile/Edit.vue`: tela de perfil do painel administrativo.
  - `resources/js/Pages/Reader/Profile.vue`: tela de perfil da área do leitor.
  - `resources/js/Pages/Public/Profile.vue`: perfil público com paginação de posts.
  - `resources/js/Pages/Post.vue`: cabeçalho de autor dinâmico e integração do `AuthorBox`.
  - `resources/js/Pages/Reader/Dashboard.vue`: inclusão do botão "Editar Perfil" e avatar atualizado.

---

## 2. Testes e Validação

- `php artisan test tests/Feature/UserProfileTest.php`: 6 testes aprovados (22 asserções).
- `php artisan test tests/Feature/PublicProfileTest.php`: 5 testes aprovados (41 asserções).
- `php artisan test tests/Feature/ImagensDeConteudoTest.php`: 10 testes aprovados (sem regressão).
- `npm run build`: build Vite concluído sem erros.
- `.\vendor\bin\pint`: conformidade de estilo PHP Laravel garantida.

