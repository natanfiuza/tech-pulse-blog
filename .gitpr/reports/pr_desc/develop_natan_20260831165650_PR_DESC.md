# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona perfis de usuário e dashboard do leitor
```

---

🎯 Resumo

O blog passa a distinguir leitores, autores e administradores no login e no acesso ao painel. Leitores ganham uma página "Minha Conta" com histórico de visualizações e comentários; autores continuam gerenciando apenas seus posts, enquanto admins têm gestão total de usuários, posts e categorias. Também habilita o cadastro self-service e preserva conteúdo ao remover usuários (soft delete).

🛠️ Mudanças Técnicas

- Adiciona coluna `role` em `users` (padrão `leitor`) com migração que converte os usuários existentes em `admin`, e habilita `SoftDeletes`.
- Cria tabela/modelo `post_views` para registrar visualizações de usuários logados.
- Adiciona middleware `role` e protege rotas `/admin` com `role:autor,admin` (categorias e gestão de usuários apenas `admin`).
- Cria rotas de cadastro (`/register`), dashboard do leitor (`/minha-conta`) e gestão de usuários (`/admin/users`).
- Cria `DashboardController`/página `Reader/Dashboard` com histórico de visualizações e comentários.
- Cria `UserController`/página `Admin/Users` para alterar papel e remover usuários, com proteção contra remoção de si mesmo ou de outros admins.
- Centraliza redirecionamento pós-login/cadastro via helper `caminho_inicial_do_usuario`.
- Ajusta `PostController`: autores enxergam apenas os próprios posts, admin vê todos; adiciona autorização para editar/excluir e registro de visualização.
- Eager load de `user` nos posts e ajustes de frontend: exibição do autor, tratamento de "Usuário removido", link dinâmico da navbar, nova página de registro e correção da URL base do Ziggy.

⚠️ Impacto/Avisos

- Banco de dados: rodar `php artisan migrate` (3 novas migrations).
- Dependências: nenhuma nova.
- Env: nenhuma variável nova.
- A partir desta mudança, novos cadastros nascem como leitores e não acessam o painel `/admin`. Usuários pré-existentes foram convertidos para admin.
- Autores perdem acesso a posts de outros usuários; apenas admins podem gerenciar todos os posts.
- Usuários removidos usam soft delete: posts e comentários permanecem, mas o nome exibido vira "Usuário removido".

close #87