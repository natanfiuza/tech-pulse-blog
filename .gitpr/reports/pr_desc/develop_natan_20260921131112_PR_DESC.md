# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: integra NewsletterBox real na página de post com flash
```

---

## 🎯 Resumo

A caixa de newsletter exibida na sidebar do post era apenas uma simulação de frontend: o formulário marcava `inscrito = true` localmente e exibia "Inscrito!" sem nunca enviar o e-mail a lugar nenhum. Isso gerava uma falsa percepção de cadastro para o leitor e deixava a página de post como o único ponto do blog com um formulário desconectado do backend já existente.

Esta mudança substitui esse mock pelo componente `NewsletterBox` real, unificando o comportamento de inscrição em todo o site e tornando o cadastro de fato persistido, com feedback apropriado ao usuário.

## 🛠️ Mudanças Técnicas

- `resources/js/Pages/Post.vue`: remove o formulário inline, o estado local (`email_newsletter`, `inscrito`) e a função `inscrever_newsletter` (mock de Fase 4); passa a renderizar `<NewsletterBox current_lang="pt_br" />` dentro do `SidebarPanel` e importa o componente.
- `app/Http/Middleware/HandleInertiaRequests.php`: compartilha globalmente as chaves de flash `newsletter`, `success` e `error` (avaliadas via closure/lazy) para permitir feedback consistente após o submit.
- `resources/js/Components/NewsletterBox.vue`: adiciona `onSuccess` ao `form.post`, limpando o campo `email` após o envio bem-sucedido e evitando reenvio acidental do mesmo endereço.
- Remoção de relatórios internos de PR e de sessão de desenvolvimento em `.gitpr/` e `docs/claude-code/reports/`.

## ⚠️ Impacto/Avisos

- **Sem alterações em banco de dados ou migrations.**
- **Sem novas dependências** nem variáveis de ambiente adicionadas.
- **Contrato Inertia ampliado:** a prop global `flash` agora expõe `newsletter`, `success` e `error`. Chaves de sessão com esses nomes passam a ser visíveis a qualquer página; evitar sobrescrever `success`/`error` com dados sensíveis.
- **Comportamento alterado para o usuário:** a inscrição na página de post deixa de ser apenas visual e passa a exigir que a rota/backend de newsletter esteja operante — sem isso, o formulário reportará erro em vez do antigo "Inscrito!" simulado.
- Remoção dos arquivos de relatório é interna e não afeta a aplicação em runtime.


close #114

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)