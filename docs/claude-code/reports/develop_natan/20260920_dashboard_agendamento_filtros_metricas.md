# Relatório de Implementação: Dashboard, Agendamento de Posts, Filtros e Métricas

- **Data:** 2026-09-20
- **Branch:** `develop_natan`
- **Tarefa:** Implementação das funcionalidades do Dashboard, Inclusão de Posts Agendados, Filtros de Status, Restrições de Acesso por Role (Admin vs Autor vs Leitor), Gráfico de Visualizações e Gerenciamento de Comentários.

---

## 1. O que foi feito

### 1.1 Inclusão e Gestão de Posts Agendados
- **Frontend (`PostsCreate.vue` e `PostsEdit.vue`):**
  - Adicionado suporte reativo à data/hora de agendamento (`published_at`).
  - Botão de ação primária adapta-se dinamicamente para **"Agendar Post"** (com ícone de agendamento) quando uma data futura é informada.
  - Alerta visual explicativo informando que o post será publicado automaticamente na data programada.
  - O salvamento de rascunho continua preservado independentemente de agendamento.
- **Backend (`PostController.php`):**
  - Validação ajustada para exigir data futura quando o status requisitado for `agendado`.
  - Método `normalizar_status` refinado para garantir que rascunhos continuem como rascunhos e que datas futuras configurem status `agendado`.

### 1.2 Restrição de Categorias por Papel (Role)
- **Sidebar (`Sidebar.vue`):** O link de navegação "Categorias" agora é renderizado condicionalmente apenas se `usuario_logado?.role === 'admin'`.
- **Dashboard (`AdminHome.vue`):**
  - O card estatístico "Categorias" é exibido somente para usuários administradores.
  - A ação rápida "Gerenciar categorias" é exibida exclusivamente para o perfil `admin`.

### 1.3 Filtros por Status na Gestão de Posts (`PostsIndex.vue`)
- Adicionadas abas de navegação/filtro por status: **Todos**, **Publicados**, **Rascunhos** e **Agendados**, com contadores dinâmicos para cada status.
- Sincronização automática com a URL através de query string (`?status=publicado`, `?status=rascunho`, `?status=agendado`).
- Mensagens de lista vazia contextualizadas conforme o status filtrado.

### 1.4 Cards Clicáveis no Dashboard
- No `AdminHome.vue`, os cards de "Posts publicados", "Rascunhos" e "Agendados" funcionam como links diretos que levam para `/admin/posts?status={status}`.
- O card de "Categorias" leva diretamente para `/admin/categories`.

### 1.5 Registro e Gráfico de Visualizações
- **Backend (`AdminController.php` & `Post.php`):**
  - Cálculo dinâmico das métricas no banco de dados.
  - Para autores: métricas e histórico de visualizações isolados estritamente para os posts do autor.
  - Para administradores: métricas e histórico de visualizações agregados de todo o blog.
  - Adicionado relacionamento `views(): HasMany` no modelo `Post`.
  - Agregação dos últimos 30 dias de visualizações e listagem dos top 5 posts mais vistos.
- **Componente de Gráfico (`VisualizacoesChart.vue`):**
  - Gráfico em SVG e Tailwind com gradiente elétrico neon aderente ao design system *Midnight Pulse*.
  - Tooltip interativo no hover com data e quantidade exata de acessos.
  - Métricas de total do período e média diária.

### 1.6 Gerenciamento de Comentários pelo Leitor (`Reader/Dashboard.vue`)
- O leitor agora pode gerenciar e excluir seus próprios comentários diretamente da página `/minha-conta`.
- Integração com `ModalConfirmacao.vue` para confirmação segura antes da exclusão.

---

## 2. Testes e Validações

- **Testes Automatizados:**
  - Criada suíte de testes em `tests/Feature/DashboardTest.php` cobrindo:
    - Métricas completas para administrador (incluindo categorias e visualizações globais).
    - Métricas isoladas para autor (sem categorias e apenas visualizações de seus próprios posts).
    - Criação de post com agendamento e status correto.
    - Exclusão de comentário pelo próprio leitor.
  - Executados testes de perfil (`UserProfileTest` e `PublicProfileTest`) com 100% de sucesso.
- **Build de Produção:**
  - Executado `npm run build` do Vite com sucesso.
- **Formatação:**
  - Executado Laravel Pint para garantir aderência aos padrões de código PHP.

