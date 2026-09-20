# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona perfil público de autor e gerenciamento de avatar
```

---

## 🎯 Resumo

Introduz uma identidade pública para autores e leitores do blog, permitindo que cada usuário tenha uma página de perfil acessível em `/{username}` com seus dados autorizados e artigos publicados. A mudança resolve a ausência de autoria rastreável nos posts (antes fixa em "Nataniel Fiuza") e elimina a dependência de imagens estáticas, passando a gerar/servir avatares próprios a partir do UUID do usuário. Também dá ao autor controle explícito de privacidade sobre nome, e-mail, bio e links sociais exibidos no blog e na caixa de autor dos artigos.

## 🛠️ Mudanças Técnicas

- **Modelo de perfil**: novo `UserProfile` (tabela `user_profiles`) com `username` único, `bio`, `social_links` (JSON) e flags de visibilidade (`public_profile_enabled`, `show_email`, `show_name`, `show_author_box`).
- **UUID em usuários**: coluna `uuid` única adicionada em `users`, com backfill de registros existentes e geração automática via `booted()`; `User` ganha relação `profile()`, `obter_ou_criar_perfil()` (com sugestão de username único a partir do nome) e accessor `avatar_url`.
- **AvatarService**: geração de SVG padrão com iniciais em fundo pastel determinístico, download do avatar do Google com fallback para o padrão, upload de imagem e resolução de caminho/URL.
- **Endpoints**: `GET /user/avatar/{uuid}` serve a imagem (com cache de 24h e detecção de MIME/SVG); `GET|PUT|POST /admin/perfil` e `/minha-conta/perfil` para edição (admin e leitor); `GET /{username}` como rota catch-all declarada por último, com bloqueio de palavras reservadas e de perfil desabilitado/soft-deleted.
- **Frontend (Inertia/Vue)**: novas páginas `Admin/Profile/Edit`, `Reader/Profile` e `Public/Profile`; componentes `AuthorBox` e `Profile/ProfileMasterDetails` (master-details com abas de identidade, redes e privacidade, upload com preview e validação); Navbar, Sidebar e Dashboard passam a exibir o avatar real do usuário.
- **Autoria dinâmica em posts**: `PostController@show` carrega `user.profile`; `Post.vue` usa nome/username, avatar e link de perfil do autor conforme regras de visibilidade.
- **Configuração**: novo `techpulse.imagens_perfil` (`data/user_profile_images`) definindo a pasta flat no disco `local`.
- **Testes**: `PublicProfileTest` (exibição, privacidade de nome/e-mail, desabilitação e palavras reservadas) e `UserProfileTest` (criação automática de perfil/uuid, iniciais do avatar, rota de imagem, atualização, username reservado e upload/reset).

## ⚠️ Impacto/Avisos

- **Banco de dados**: requer `php artisan migrate`. As migrações adicionam `users.uuid` (com backfill) e criam `user_profiles` (FK `user_id` com `cascadeOnDelete`). O `down` remove a coluna `uuid` sem restaurar o estado anterior.
- **Armazenamento**: avatares são persistidos no disco `local` em `storage/app/data/user_profile_images/{uuid}` (arquivos sem extensão). É necessário garantir permissão de escrita e backup dessa pasta.
- **Chamadas externas**: `AvatarService::salvar_avatar_google` faz requisição HTTP (timeout de 10s) para baixar o avatar do Google; falhas caem no avatar padrão e geram log de warning.
- **Rotas**: a rota `/{username}` é catch-all e precisa permanecer como a última declarada em `web.php` para não capturar rotas de primeiro nível. Novas rotas de nível raiz devem ser adicionadas acima dela.
- **Config/Env**: usa `config('techpulse.imagens_perfil')`; o valor pode ser sobrescrito por env, mas não há variável obrigatória nova.
- **Frontend**: novas páginas Inertia e componentes Vue exigem rebuild dos assets (`npm run build`).
- **Seeders**: `CreateUsersSeeder` agora atribui explicitamente `ROLE_ADMIN` ao usuário seed e cria seu perfil; execuções anteriores geravam usuário sem papel definido.

close #102

---

[![GitPR](https://img.shields.io/badge/GitPR-0_errors_%C2%B7_39_warnings-yellow)](https://gitpr.natanfiuza.dev.br/)