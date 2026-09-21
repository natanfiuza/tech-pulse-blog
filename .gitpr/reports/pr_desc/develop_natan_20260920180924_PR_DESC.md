# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona dashboard com métricas e agendamento de posts
```

---

## 🎯 Resumo

O painel administrativo exibia dados estáticos (números fixos em `data()`) e não oferecia visão real da operação editorial. Esta mudança transforma o dashboard em um painel orientado a dados, com métricas calculadas em tempo real, gráfico de audiência e navegação direta para listas filtradas.

Além disso, o fluxo de agendamento de publicações foi corrigido para ser explícito e seguro: o backend agora exige data futura quando o status é `agendado`, e o frontend reflete o estado de agendamento no botão e nas mensagens. Autores passam a ver apenas as próprias métricas, enquanto administradores mantêm a visão global — resolvendo vazamento de escopo entre papéis.

Por fim, leitores ganharam autonomia para excluir os próprios comentários via modal de confirmação, sem depender de suporte manual.

## 🛠️ Mudanças Técnicas

- **Dashboard real (`AdminController@index`)**: calcula posts publicados, rascunhos, agendados, categorias, total de visualizações, série diária dos últimos 30 dias e top 5 posts mais vistos; consulta escopada por `possui_papel(ROLE_ADMIN)` (admin vê tudo, autor vê apenas os próprios posts).
- **Novo relacionamento `Post::views()`**: `HasMany` para `PostView`, habilitando `withCount('views')` no ranking de mais vistos.
- **Padronização de status em `PostController`**: `normalizar_status()` agora prioriza `rascunho`, converte qualquer data futura em `agendado` e rebaixa `agendado` vencido para `publicado`; validação de `published_at` é `required|date|after:now` quando o status solicitado é `agendado`, com mensagens de erro dedicadas.
- **Filtro de status na listagem**: `PostController@index` repassa `status_filtro` e `PostsIndex.vue` implementa abas com contagem, sincronização via querystring (`window.history.replaceState`) e exibição da data de agendamento na listagem.
- **Novo componente `VisualizacoesChart.vue`**: gráfico SVG nativo (sem bibliotecas externas), com área em gradiente, curvas suavizadas, tooltip interativo, total do período e média diária.
- **UI reativa em `AdminHome.vue`**: cards e status agora derivados de props, clicáveis e com atalhos para listas filtradas; itens de Categorias ocultados para não-admins em `Sidebar.vue`.
- **UX de agendamento**: `PostsCreate.vue` e `PostsEdit.vue` detectam data futura, alteram rótulo/ícone do botão e exibem aviso âmbar; enviam `status: agendado` automaticamente.
- **Exclusão de comentários**: `Reader/Dashboard.vue` integra `ModalConfirmacao` e `useForm().delete()` com `preserveScroll`, além de suporte a mensagens flash de sucesso/erro.
- **Testes**: novo `DashboardTest` cobrindo métricas de admin, escopo por autor, criação de post agendado e exclusão de comentário próprio.
- **Limpeza**: remoção de linhas em branco finais em arquivos de Newsletter, Mail, Models e migrations (sem alteração funcional).

## ⚠️ Impacto/Avisos

- **Banco de dados**: nenhuma migration nova. O relacionamento `Post::views()` depende da tabela `post_views` já existente.
- **Dependências**: nenhuma adicionada — o gráfico é SVG puro com Vue 3.
- **Variáveis de ambiente**: nenhuma alterada.
- **Comportamento de status**: a regra de normalização ficou mais restritiva. Posts com status `agendado` e `published_at` no passado serão convertidos para `publicado` em qualquer salvamento — validar se há registros legados nessa condição.
- **Performance**: o dashboard executa múltiplas queries agregadas por acesso; com volume alto de `post_views`, recomenda-se acompanhar índices em `post_views.post_id` e `post_views.viewed_at`.
- **Permissões**: autores deixam de ver a contagem global de categorias e o link de gestão de categorias (retorna 0/oculto); esperado pelo novo escopo, mas confirmar com stakeholders.


close #110

---

[![GitPR](https://img.shields.io/badge/GitPR-0_errors_%C2%B7_7_warnings-yellow)](https://gitpr.natanfiuza.dev.br/)