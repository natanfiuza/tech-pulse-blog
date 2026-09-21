# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona página dedicada de inscrição na newsletter
```

---

## 🎯 Resumo

A newsletter era acessível apenas como uma âncora (`/#newsletter`) na home, o que limitava a experiência de cadastro e o compartilhamento direto do link. Esta mudança cria uma **página pública dedicada e indexável** para a newsletter, com URL própria (`/newsletter`), suporte a idioma via query string e reaproveitamento do componente de inscrição já existente — sem duplicar lógica de submissão.

Com isso, os CTAs do Navbar e do Footer passam a apontar para uma landing page focada em conversão, reforçando proposta de valor (conteúdo técnico, IA, privacidade) antes do formulário.

## 🛠️ Mudanças Técnicas

- **Rota**: adiciona `GET /newsletter` nomeada `newsletter.page`, apontando para `NewsletterController@page`.
- **Controller**: novo método `page()` que normaliza o idioma (`?lang=`, padrão `pt_br`), resolve as strings via `NewsletterTranslations` e carrega apenas categorias raiz (`parent_id IS NULL`, selecionando `id`, `name`, `slug`).
- **Frontend**: nova página Inertia `resources/js/Pages/NewsletterPage.vue`, composta por `Navbar`, `NewsletterBox` (mesmo componente de inscrição) e `Footer`, com fallbacks de textos quando `ui_strings` não está disponível.
- **Navegação**: links de newsletter em `Navbar.vue` e `Footer.vue` migrados de `href="/#newsletter"` para `route('newsletter.page')`.
- **Testes**: cobertura de renderização da página (assert Inertia com component, `categorias`, `ui_strings` e `current_lang`) e do parâmetro de idioma customizado.

## ⚠️ Impacto/Avisos

- **Banco de dados**: nenhuma migration ou alteração de schema. Há uma nova query de leitura em `categories`, filtrada por categorias raiz.
- **Envs / Configuração**: nenhuma variável de ambiente adicionada ou alterada.
- **Dependências**: nenhuma nova dependência de backend ou frontend.
- **Roteamento**: nova rota pública `GET /newsletter`; verificar regras de cache/CDN e sitemap caso existam.
- **Compatibilidade**: o link antigo `/#newsletter` deixa de ser usado pela interface, mas a âncora na home permanece funcional — avaliar redirecionamento permanente (301) para `newsletter.page`.
- **Observação de i18n**: alguns textos de benefícios estão hardcoded em português na nova página, enquanto título/descrição usam `ui_strings`; recomenda-se migrá-los para o mecanismo de tradução para consistência.

close #120

---

[![GitPR](https://img.shields.io/badge/GitPR-0_errors_%C2%B7_3_warnings-yellow)](https://gitpr.natanfiuza.dev.br/)