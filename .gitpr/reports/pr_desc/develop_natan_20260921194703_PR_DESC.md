# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige offset do topo em páginas com navbar fixa
```

---

## 🎯 Resumo

O conteúdo das páginas de Login, Registro e Artigo ficava parcialmente encoberto pela barra de navegação fixa no topo, prejudicando a leitura e a usabilidade logo no primeiro contato do usuário com a aplicação. Este ajuste alinha o espaçamento superior das páginas ao protótipo (`tela_artigo`), garantindo que títulos e formulários apareçam integralmente abaixo do header, tanto em desktop quanto em mobile.

## 🛠️ Mudanças Técnicas

- `resources/js/Pages/Auth/Login.vue`: substitui `py-12` por `pt-28 md:pt-32 pb-12` no `<main>`.
- `resources/js/Pages/Auth/Register.vue`: aplica o mesmo padrão de padding superior responsivo do Login.
- `resources/js/Pages/Post.vue`: substitui `py-12` por `pt-32 pb-12` no `<main>`, com comentário documentando o cálculo (80px da barra fixa + 48px de respiro) conforme protótipo `tela_artigo`.
- Uso de breakpoint `md:` nas telas de autenticação para diferenciar o respiro em viewports menores.

## ⚠️ Impacto/Avisos

- **Sem alterações em banco de dados, variáveis de ambiente, rotas ou dependências** — mudança restrita a classes utilitárias de CSS (Tailwind) em componentes Vue.
- Os valores de padding são acoplados à altura da navbar fixa; se a altura do header mudar, esses valores precisarão ser revalidados.
- Validação visual recomendada em mobile, tablet e desktop nas três telas afetadas, incluindo cenários de scroll com sidebar no artigo.

close #118

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)