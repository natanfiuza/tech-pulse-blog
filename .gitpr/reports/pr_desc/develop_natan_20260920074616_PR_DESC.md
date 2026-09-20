# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: substitui window.confirm por modal de confirmação reutilizável
```

---

## 🎯 Resumo
A exclusão de conteúdo no painel administrativo e na área pública dependia do `window.confirm` nativo, que além de visualmente desconectado do design system, não oferece contexto sobre o que está sendo removido nem feedback de progresso. Este PR cria um componente único e reutilizável de confirmação, garantindo consistência visual, acessibilidade e uma experiência de exclusão mais segura — com destaque do item afetado, avisos contextuais sobre efeitos colaterais (posts, subcategorias, respostas aninhadas) e estado de carregamento durante a requisição.

## 🛠️ Mudanças Técnicas
- Adiciona o componente `ModalConfirmacao.vue` com `Teleport` para o `body`, transições de entrada/saída, spinner de processamento e props configuráveis (`titulo`, `mensagem`, `item_nome`, `aviso_extra`, `rotulo_confirmar`, `rotulo_cancelar`, `processando`).
- Implementa acessibilidade: `role="dialog"`, `aria-modal`, `aria-labelledby` com ID único, fechamento por tecla `Escape` e clique no backdrop (bloqueados durante o processamento), além de travamento do scroll do body enquanto aberto.
- Substitui o `window.confirm` por `ModalConfirmacao` em `Admin/Categories/CategoriesIndex.vue`, `Admin/Posts/PostsIndex.vue` e `Admin/Users.vue`, padronizando o fluxo em `abrir_modal_exclusao` / `cancelar_exclusao` / `confirmar_exclusao`.
- Refatora `CommentThread.vue` para emitir o evento `solicitar_exclusao` (propagado recursivamente pelos comentários filhos) em vez de executar a exclusão diretamente, centralizando a lógica em `CommentSection.vue`.
- Adiciona em `CommentSection.vue` o estado do modal, o preview truncado do comentário (80 caracteres) e o aviso condicional quando existem respostas aninhadas.
- Aplica `preserveScroll: true` em todas as requisições de exclusão para manter a posição de rolagem do usuário após remover um item de listas longas.
- Renomeia a ação de usuários de "Excluir" para "Remover" (ícone `person_remove`), refletindo que o cadastro é revogado e o conteúdo histórico é preservado.
- Ajustes de formatação em `Admin/Topbar.vue` e adição de `cursor-pointer` nos botões de ação.

## ⚠️ Impacto/Avisos
- **Banco de dados:** nenhuma migration ou alteração de schema.
- **Variáveis de ambiente:** nenhuma nova variável exigida.
- **Dependências:** nenhuma dependência adicionada ou atualizada.
- **Comportamento:** as rotas `comments.destroy`, `categories.destroy`, `posts.destroy` e `users.destroy` permanecem inalteradas; apenas a camada de confirmação no frontend mudou. Exclusões agora só ocorrem mediante ação explícita no modal.
- **UX:** a remoção de usuário deixa de ser chamada de "exclusão", o que pode exigir alinhamento com textos de ajuda ou documentação existentes.


close #106
---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)