# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona dashboard com métricas e agendamento de posts
```

---

## 🎯 Resumo

O dashboard administrativo exibia dados estáticos (hardcoded), o que impedia gestores e autores de acompanharem a real performance dos conteúdos. Esta mudança conecta a tela a dados reais de posts, categorias e visualizações, com escopo de acesso por papel: administradores veem o panorama global do blog, enquanto autores veem apenas seus próprios números.

Além disso, o fluxo de agendamento de publicação foi corrigido e explicitado na interface — antes o comportamento era implícito e sujeito a inconsistências entre o que o formulário enviava e o que o backend normalizava — e o leitor passa a poder gerenciar (excluir) seus próprios comentários.

## 🛠️ Mudanças Técnicas

- **AdminController::index** deixa de apenas renderizar a view e passa a agregar métricas reais: posts publicados, rascunhos, agendados, total de visualizações e contagem de categorias, sempre filtradas por `user_id` quando o usuário não é admin.
- **Série histórica de 30 dias** construída no backend com janela contínua (dias sem acessos aparecem com total zero), alimentando o gráfico do frontend; inclusão de ranking dos 5 posts mais vistos via `withCount('views')`.
- Novo componente **`VisualizacoesChart.vue`**: gráfico SVG com linha suavizada (curvas de Bézier), área com gradiente, tooltip interativo, total do período e média diária.
- Novo relacionamento **`Post::views()`** (`HasMany` para `PostView`).
- **`AdminHome.vue`** passa a consumir props do servidor, com cards clicáveis que levam à listagem já filtrada por status, seção de posts mais vistos e ocultação de itens exclusivos de admin (categorias) para autores.
- **`PostsIndex.vue`** ganha abas de filtro por status (com contagem), sincronizadas com a query string via `history.replaceState`, além de exibir a data de agendamento.
- **Validação de agendamento** em `store` e `update`: `published_at` passa a ser obrigatório e obrigatoriamente futuro quando o status for `agendado`, com mensagens de erro traduzidas.
- **`normalizar_status` corrigido**: rascunhos nunca são promovidos a agendado, e um agendamento com data passada é rebaixado para publicado.
- **`PostsCreate.vue` / `PostsEdit.vue`**: labels, ícones e avisos contextuais mudam dinamicamente conforme o post seja publicação imediata, agendamento ou edição de conteúdo já publicado.
- **`Reader/Dashboard.vue`**: exclusão de comentário próprio com modal de confirmação e feedback via flash messages.
- **Sidebar** exibe "Categorias" apenas para administradores.
- Cobertura de testes em **`tests/Feature/DashboardTest.php`** validando métricas por papel, criação de post agendado e exclusão de comentário pelo leitor.

## ⚠️ Impacto/Avisos

- **Banco de dados**: o dashboard depende da tabela `post_views` (colunas `post_id`, `user_id`, `viewed_at`) e do relacionamento `Comment`/`User`. Caso a migration de `post_views` não esteja aplicada, o carregamento da home administrativa falhará.
- **Papéis de usuário**: a lógica usa `User::ROLE_ADMIN`, `User::ROLE_AUTOR` e `User::ROLE_LEITOR`; é necessário que a coluna `role` esteja populada corretamente, sob risco de autores visualizarem dados agregados indevidos.
- **Rota**: o botão de exclusão de comentário depende de `comments.destroy`.
- **Performance**: a contagem de visualizações trafega as linhas do período (30 dias) para agregação em PHP; em blogs com alto tráfego recomenda-se migrar para agregação via SQL (`GROUP BY DATE(viewed_at)`).
- **Sem novas variáveis de ambiente ou dependências externas** adicionadas.