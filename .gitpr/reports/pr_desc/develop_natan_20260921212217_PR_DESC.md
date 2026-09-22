# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona fluxo de solicitação e aprovação de autores
```

---

## 🎯 Resumo

O blog não tinha um caminho formal para um leitor se tornar autor: o interessado precisava sair do site e enviar um e-mail manual para a equipe, o que gerava atrito, perdia rastreabilidade e dependia de comunicação fora da plataforma. Esta mudança cria um fluxo completo e auditável dentro do TechPulse — o leitor envia sua motivação, o admin revisa em um painel dedicado e a promoção de papel acontece automaticamente na aprovação — reduzindo trabalho manual e dando visibilidade ao funil de novos colaboradores.

## 🛠️ Mudanças Técnicas

- **Banco de dados**: nova migration `create_solicitacoes_autor_table` com `user_id` (cascadeOnDelete), `motivacao`, `status` (enum `pendente|aprovada|rejeitada`), `admin_id` (nullable, nullOnDelete), `admin_nota` e timestamps.
- **Model `SolicitacaoAutor`**: constantes de status, `fillable`, relacionamentos `user()`/`admin()` com `withTrashed()`, scope `pendente()` e helper `esta_pendente()`.
- **`SolicitacaoAutorController`**: `show` (página pública com estado do solicitante), `store` (valida papel `leitor`, bloqueia solicitação pendente duplicada, exige motivação de 50 a 2000 caracteres), `index` (listagem com eager loading de usuário e admin) e `update` (aprova/rejeita; na aprovação eleva o usuário para `ROLE_AUTOR`).
- **Rotas**: `GET/POST /tornar-se-autor` (POST protegido por `auth`) e grupo admin `/admin/solicitacoes-autor` (GET e PUT).
- **Frontend**: novas páginas `Auth/TornarSeAutor.vue` e `Admin/SolicitacoesAutor.vue`, além do componente reutilizável `FormularioSolicitacao.vue`; a página de destino cobre todos os estados (visitante com cadastro embutido, leitor, pendente, rejeitado, autor e admin).
- **`RegisteredUserController`**: suporte a `?next=` com lista branca de destinos, permitindo que o cadastro iniciado em `/tornar-se-autor` retorne à solicitação.
- **Dashboard admin**: novo card de "Solicitações de Autor" com contagem de pendentes (grid ajustado de 4 para 5 colunas) e atalho na lista de ações.
- **Home**: CTA antes apontava para `mailto:` e agora leva à página interna de solicitação, corrigindo também o gradiente do card no tema claro.

## ⚠️ Impacto/Avisos

- **Migração obrigatória**: é preciso rodar `php artisan migrate` antes do deploy; a tabela `solicitacoes_autor` não existia. O `down()` apenas remove a tabela.
- **Enums e chaves estrangeiras**: o `status` usa `enum` do MySQL — alterações futuras de valores exigem nova migration. A exclusão de um usuário apaga suas solicitações; a de um admin apenas anula `admin_id`.
- **Segurança**: o redirecionamento pós-cadastro via `?next=` é restrito a uma lista branca fixa (`/tornar-se-autor`); novos destinos exigem atualização explícita desse array.
- **Regra de negócio**: a aprovação promove o usuário diretamente para `autor`, concedendo acesso ao painel de publicação — revisar se a moderação de posts já cobre esse novo papel.
- **Sem novas dependências, variáveis de ambiente ou comandos de build adicionais**.


close #125

---

[![GitPR](https://img.shields.io/badge/GitPR-0_errors_%C2%B7_10_warnings-yellow)](https://gitpr.natanfiuza.dev.br/)