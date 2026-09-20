# Especificação Técnica e Funcional: Estrutura de Privilégios e Papéis de Usuários

> **Status:** Aprovado / Em Produção  
> **Versão:** 1.0.0  
> **Data:** 2026-09-20  
> **Público-alvo:** Desenvolvedores (Backend/Frontend), Analistas de Qualidade (QA), Product Owners (PO) e Arquitetos  
> **Módulos Relacionados:** Autenticação, Perfis de Usuário, Painel Administrativo, Comentários e Gestão de Posts  

---

## 1. Visão Geral e Objetivos

Este documento é a **especificação oficial e fonte da verdade** sobre o modelo de controle de acesso baseado em papéis (*Role-Based Access Control* - RBAC) do **Tech Pulse Blog**.

O objetivo deste sistema é garantir a segregação de responsabilidades e privilégios entre diferentes tipos de pessoas que interagem com a plataforma, abrangendo desde o leitor casual e comentarista até o autor de conteúdo e o administrador geral do blog.

### Objetivos Principais
1. **Segurança e Isolamento de Recursos:** Assegurar que nenhum usuário execute ações ou visualize dados além dos permitidos pelo seu papel.
2. **Autoatendimento Seguro (Self-Service):** Permitir cadastro público sem conceder privilégios administrativos indevidos.
3. **Integridade de Conteúdo:** Preservar comentários e postagens públicas mesmo em caso de desativação de conta (*Soft Delete*).
4. **Proteção Contra Lockout:** Impedir que administradores bloqueiem a si mesmos ou excluam outros administradores por acidente ou disputa de permissões.

---

## 2. Estrutura do Campo `role` no Banco de Dados

A identificação de privilégios é armazenada diretamente na tabela `users`, na coluna `role`.

### 2.1. Definição no Esquema do Banco (`schema`)
- **Tabela:** `users`
- **Coluna:** `role`
- **Tipo de Dado:** `VARCHAR(255)` (String)
- **Valor Padrão (*Default*):** `'leitor'`
- **Nullable:** `NOT NULL`
- **Migração:** `database/migrations/2026_08_31_000001_add_role_to_users_table.php`

### 2.2. Valores Válidos e Constantes no Modelo (`User.php`)

No backend PHP/Laravel, os valores são mapeados pelas constantes de classe no modelo `App\Models\User`:

```php
namespace App\Models;

class User extends Authenticatable
{
    public const ROLE_LEITOR = 'leitor';
    public const ROLE_AUTOR  = 'autor';
    public const ROLE_ADMIN  = 'admin';

    public const ROLES = [
        self::ROLE_LEITOR,
        self::ROLE_AUTOR,
        self::ROLE_ADMIN,
    ];
}
```

| Valor Gravado no Banco (`role`) | Constante PHP | Descrição Funcional |
| :--- | :--- | :--- |
| `'leitor'` | `User::ROLE_LEITOR` | Usuário padrão da plataforma. Consome conteúdo, comenta, vota e possui dashboard pessoal (`/minha-conta`). **Não acessa o painel administrativo.** |
| `'autor'` | `User::ROLE_AUTOR` | Produtor de conteúdo. Tem acesso ao painel `/admin` para criar, editar, excluir e gerenciar **exclusivamente os seus próprios posts**. Não altera posts alheios nem taxonomias globais (categorias). |
| `'admin'` | `User::ROLE_ADMIN` | Administrador total da plataforma. Gerencia todos os posts de qualquer autor, toda a árvore de categorias e a tabela de usuários (promovendo/rebaixando papéis e aplicando soft delete). |

---

## 3. Fluxos de Cadastro e Atribuição de Regra Inicial

Quando uma pessoa se cadastra no Tech Pulse Blog, **qual regra ela recebe?**

> **Regra Fundamental:**  
> **Todo novo usuário cadastrado recebe, obrigatoriamente e por padrão, o papel `leitor` (`User::ROLE_LEITOR`).**  
> Ninguém se autoatribui papel de `autor` ou `admin`. Privilégios elevados são concedidos exclusivamente por um `admin` já existente através da interface de gestão em `/admin/users`.

### 3.1. Cadastro Tradicional Self-Service (`/register`)
1. **Página:** `GET /register` (`Auth/Register.vue`)
2. **Controlador:** `App\Http\Controllers\Auth\RegisteredUserController@store`
3. **Payload Enviado:** `name`, `email`, `password`, `password_confirmation`
4. **Execução:**
   - O controlador executa `User::create([...])` fornecendo apenas nome, e-mail e hash da senha.
   - O banco de dados aplica o valor padrão da coluna: `role = 'leitor'`.
   - O modelo dispara o evento de ciclo de vida (`booted -> created`), criando automaticamente o perfil (`UserProfile`) e gerando o avatar inicial via `AvatarService`.
   - É efetuado o login automático da sessão (`Auth::login($user)`).
   - O helper `caminho_inicial_do_usuario($user)` redireciona o usuário recém-criado para `/minha-conta`.

### 3.2. Cadastro / Login via OAuth Google (`/login/google`)
1. **Redirecionamento:** `GET /login/google` (`SocialiteController@redirect_to_google`)
2. **Retorno do Provedor:** `GET /login/google/callback` (`SocialiteController@handle_google_callback`)
3. **Execução:**
   - O sistema recebe os dados do Google (ID, Nome, E-mail, Avatar).
   - Executa `User::updateOrCreate(['google_id' => $googleUser->getId()], [...])`.
   - Se for uma conta inédita: a coluna `role` assume o default do banco (`'leitor'`).
   - O avatar do Google é baixado e sincronizado no armazenamento local do usuário.
   - É efetuado o login com sessão persistente (`Auth::login($user, true)`).
   - O redirecionamento envia o leitor para `/minha-conta`.

### 3.3. Criação de Administradores Iniciais (*Seeders* e Migrações)
- **Seeder Inicial:** O seeder `Database\Seeders\CreateUsersSeeder` é responsável por criar a conta master do sistema com e-mail `techpulse@natanfiuza.dev.br` e define explicitamente `'role' => User::ROLE_ADMIN`.
- **Migração de Histórico:** A migração `2026_08_31_000001_add_role_to_users_table.php` executou um backfill inicial em base legada promovendo todos os usuários até então existentes a `admin` para evitar interrupções de acesso prévias à implementação dos papéis.

---

## 4. Matriz Completa de Permissões e Privilégios

A tabela a seguir consolida todas as ações possíveis no sistema cruzadas com os perfis de acesso.

| Módulo / Recurso | Ação do Sistema | Visitante (Anônimo) | Leitor (`leitor`) | Autor (`autor`) | Admin (`admin`) |
| :--- | :--- | :---: | :---: | :---: | :---: |
| **Leitura Pública** | Ler posts publicados | ✅ | ✅ | ✅ | ✅ |
| | Visualizar categorias e tags | ✅ | ✅ | ✅ | ✅ |
| | Visualizar perfil público de autores | ✅ | ✅ | ✅ | ✅ |
| | Ler comentários públicos | ✅ | ✅ | ✅ | ✅ |
| **Interação** | Publicar comentário em post | ❌ | ✅ | ✅ | ✅ |
| | Excluir o próprio comentário | ❌ | ✅ | ✅ | ✅ |
| | Excluir comentário de terceiros | ❌ | ❌ | ❌ | ❌ *(1)* |
| | Votar / Desvotar em comentário (Upvote) | ❌ | ✅ | ✅ | ✅ |
| **Área do Leitor** | Acessar `/minha-conta` (Dashboard) | ❌ | ✅ | Redireciona *(2)* | Redireciona *(2)* |
| | Ver histórico de posts visualizados | ❌ | ✅ | ✅ | ✅ |
| | Editar dados de perfil e bio | ❌ | ✅ | ✅ | ✅ |
| | Alterar avatar pessoal | ❌ | ✅ | ✅ | ✅ |
| **Painel Administrativo**| Acessar `/admin/home` | ❌ | ❌ (403) | ✅ | ✅ |
| | Acessar `/admin/perfil` | ❌ | ❌ (403) | ✅ | ✅ |
| **Posts (Admin)** | Listar posts no painel (`/admin/posts`) | ❌ | ❌ (403) | Apenas os seus | Todos do blog |
| | Criar novo post (`/admin/posts/create`) | ❌ | ❌ (403) | ✅ | ✅ |
| | Editar post próprio | ❌ | ❌ (403) | ✅ | ✅ |
| | Editar post de outro autor | ❌ | ❌ (403) | ❌ (403) | ✅ |
| | Excluir post próprio | ❌ | ❌ (403) | ✅ | ✅ |
| | Excluir post de outro autor | ❌ | ❌ (403) | ❌ (403) | ✅ |
| | Upload de imagens de conteúdo no editor | ❌ | ❌ (403) | ✅ | ✅ |
| **Categorias (Admin)** | Acessar listagem (`/admin/categories`) | ❌ | ❌ (403) | ❌ (403) | ✅ |
| | Criar / Editar / Excluir categorias | ❌ | ❌ (403) | ❌ (403) | ✅ |
| **Gestão de Usuários** | Acessar listagem (`/admin/users`) | ❌ | ❌ (403) | ❌ (403) | ✅ |
| | Alterar papel de um leitor/autor | ❌ | ❌ (403) | ❌ (403) | ✅ |
| | Promover leitor/autor a admin | ❌ | ❌ (403) | ❌ (403) | ✅ |
| | Alterar papel de outro admin | ❌ | ❌ (403) | ❌ (403) | ❌ (403) *(3)* |
| | Alterar o próprio papel | ❌ | ❌ (403) | ❌ (403) | ❌ (403) *(3)* |
| | Excluir usuário (Soft Delete) | ❌ | ❌ (403) | ❌ (403) | ✅ *(3)* |
| | Excluir outro admin ou a si mesmo | ❌ | ❌ (403) | ❌ (403) | ❌ (403) *(3)* |

*(1) A política atual de comentários em `CommentController@destroy` autoriza apenas o autor do próprio comentário (`$comment->user_id === Auth::id()`).*  
*(2) Ao acessar `/minha-conta`, usuários com papel `autor` ou `admin` são redirecionados automaticamente para `/admin/home`.*  
*(3) Protegido pelas travas de segurança (*guardrails*) em `UserController@autorizar_gestao`.*

---

## 5. Implementação Técnica e Mecanismos de Segurança

### 5.1. Middleware de Proteção de Rotas (`EnsureUserHasRole`)
O middleware `App\Http\Middleware\EnsureUserHasRole` está registrado no kernel HTTP com o alias `'role'`.

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (! $request->user() || ! $request->user()->possui_papel(...$roles)) {
        abort(403);
    }

    return $next($request);
}
```

#### Configuração nos Grupos de Rotas (`routes/web.php`):
- **Acesso de Autores e Admins:**
  ```php
  Route::middleware(['auth', 'role:autor,admin'])->prefix('admin')->group(function () {
      Route::get('/home', [AdminController::class, 'index'])->name('admin.home');
      Route::get('/perfil', [UserProfileController::class, 'edit_admin'])->name('admin.profile.edit');
      Route::prefix('/posts')->group(...);
  });
  ```
- **Acesso Exclusivo de Administradores:**
  ```php
  Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
      Route::prefix('/categories')->group(...);
      Route::prefix('/users')->group(...);
  });
  ```

### 5.2. Método Auxiliar no Modelo `User`
O modelo `App\Models\User` encapsula a verificação de múltiplos papéis:
```php
public function possui_papel(string ...$papeis): bool
{
    return in_array($this->role, $papeis, true);
}
```

### 5.3. Autorização de Conteúdo em `PostController`
Mesmo que um `autor` acesse o painel `/admin/posts`, o controlador aplica duas travas rígidas:

1. **Filtro na Listagem (`index`):**
   ```php
   $query = Post::query();
   if (Auth::user()->possui_papel(User::ROLE_AUTOR)) {
       $query->where('user_id', Auth::user()->id);
   }
   ```
2. **Validação em Operações de Escrita/Exclusão (`autorizar_post`):**
   ```php
   private function autorizar_post(Post $post): void
   {
       if (! Auth::user()->possui_papel(User::ROLE_ADMIN) && $post->user_id !== Auth::user()->id) {
           abort(403);
       }
   }
   ```
   *Este método é executado obrigatoriamente antes de `edit`, `update` e `destroy`.*

### 5.4. Guardrails de Gestão em `UserController`
Na gestão de usuários (`/admin/users`), existem regras estritas para evitar autoexclusão ou guerras de privilégios entre administradores:

```php
private function autorizar_gestao(User $user): void
{
    // Ninguém altera/exclui a si mesmo e admin não rebaixa nem exclui outro admin
    if ($user->id === Auth::user()->id || $user->possui_papel(User::ROLE_ADMIN)) {
        abort(403);
    }
}
```

### 5.5. Redirecionamento Pós-Autenticação (`caminho_inicial_do_usuario`)
Definido em `app/helpers.php` para direcionar cada tipo de usuário ao ambiente correspondente:
```php
function caminho_inicial_do_usuario(User $user): string
{
    if (in_array($user->role, [User::ROLE_AUTOR, User::ROLE_ADMIN], true)) {
        return route('admin.home');
    }

    return route('minha_conta');
}
```

---

## 6. Tratamento de Exclusão de Contas (*Soft Delete* e Conteúdo Órfão)

Quando um usuário é removido no painel de administração:
1. **Mecanismo:** É aplicado *Soft Delete* (`deleted_at` preenchido na tabela `users`). A conta perde imediatamente o acesso ao login.
2. **Preservação de Integridade:** As postagens (`posts`) e comentários (`comments`) associados ao `user_id` **não são excluídos em cascata**.
3. **Exibição na Interface:**
   - Em cards de posts públicos (`PostCard.vue`), listagens no admin (`PostsIndex.vue`) e na árvore de comentários (`CommentThread.vue`), se o usuário associado estiver com soft delete ou for nulo, a autoria é automaticamente exibida como **"Usuário removido"** e o avatar é substituído pelo placeholder padrão.

---

## 7. Roteiro de Testes e Critérios de Aceite (QA / PO)

Os seguintes cenários devem ser utilizados para homologação e testes de regressão:

### CT-01: Cadastro de Novo Usuário Self-Service
- **Pré-condição:** Estar deslogado.
- **Passos:** Acessar `/register`, preencher nome, e-mail e senha válida e submeter.
- **Resultado Esperado:** 
  - Registro criado na tabela `users` com `role = 'leitor'`.
  - Usuário autenticado automaticamente.
  - Redirecionamento para `/minha-conta`.
  - Navbar exibe opções de Leitor (sem links para `/admin`).

### CT-02: Cadastro via Google OAuth
- **Pré-condição:** Estar deslogado.
- **Passos:** Clicar em "Entrar com Google" e autenticar com uma conta Google inédita.
- **Resultado Esperado:**
  - Registro criado na tabela `users` com `role = 'leitor'` e `google_id` preenchido.
  - Redirecionamento para `/minha-conta`.

### CT-03: Barreira de Acesso de Leitor ao Painel Admin
- **Pré-condição:** Estar logado com perfil `leitor`.
- **Passos:** Tentar navegar diretamente para as URLs:
  - `/admin/home`
  - `/admin/posts`
  - `/admin/categories`
  - `/admin/users`
- **Resultado Esperado:** Todas as requisições devem retornar **HTTP 403 Forbidden**.

### CT-04: Isolamento de Posts para o Perfil Autor
- **Pré-condição:** Estar logado como `autor` (ex: Usuário A). Existirem posts cadastrados pelo Usuário A e por outros usuários (Usuário B / Admin).
- **Passos:**
  1. Acessar `/admin/posts`.
  2. Tentar acessar a URL de edição de um post do Usuário B (`/admin/posts/edit/{uuid_do_post_b}`).
  3. Tentar submeter deleção do post do Usuário B via `DELETE /admin/posts/delete/{uuid_do_post_b}`.
- **Resultado Esperado:**
  1. A listagem exibe **apenas** os posts cujo `user_id` é o do Usuário A.
  2. O acesso direto à edição do post de terceiros retorna **HTTP 403 Forbidden**.
  3. A tentativa de exclusão retorna **HTTP 403 Forbidden**.

### CT-05: Restrição de Categorias e Usuários para Autor
- **Pré-condição:** Estar logado como `autor`.
- **Passos:** Tentar acessar `/admin/categories` ou `/admin/users`.
- **Resultado Esperado:** Requisições retornam **HTTP 403 Forbidden**. Menu lateral do painel admin não exibe links para Categorias e Usuários.

### CT-06: Acesso Pleno do Perfil Admin
- **Pré-condição:** Estar logado como `admin`.
- **Passos:** Acessar `/admin/posts`, `/admin/categories` e `/admin/users`. Editar post criado por qualquer outro autor.
- **Resultado Esperado:** Acesso permitido a todos os módulos, com visibilidade e edição de todos os posts e categorias.

### CT-07: Alteração de Papéis por Administrador
- **Pré-condição:** Estar logado como `admin`.
- **Passos:** Em `/admin/users`, alterar a role de um `leitor` para `autor` ou `admin`.
- **Resultado Esperado:**
  - Requisição `PUT /admin/users/{id}/role` executada com sucesso.
  - O usuário alterado ganha os novos privilégios no seu próximo acesso/requisição.

### CT-08: Guardrails de Segurança de Administrador
- **Pré-condição:** Estar logado como `admin` (Admin 1). Existir outro `admin` (Admin 2).
- **Passos:**
  1. Em `/admin/users`, tentar rebaixar ou excluir a própria conta (Admin 1).
  2. Tentar rebaixar ou excluir o Admin 2.
- **Resultado Esperado:**
  - Ações bloqueadas na interface (botões desabilitados/ocultos) e qualquer tentativa direta via requisição HTTP retorna **HTTP 403 Forbidden**.

### CT-09: Soft Delete e Autoria de Conteúdo
- **Pré-condição:** Existir um usuário com posts e comentários publicados.
- **Passos:** O admin exclui esse usuário em `/admin/users`.
- **Resultado Esperado:**
  - Campo `deleted_at` do usuário é preenchido.
  - O usuário não consegue mais efetuar login.
  - Os posts e comentários permanecem visíveis publicamente com a assinatura **"Usuário removido"**.

