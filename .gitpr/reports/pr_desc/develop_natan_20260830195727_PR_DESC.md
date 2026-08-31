# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona linter GitPR e protótipos da versão 3
```

---

🎯 Resumo
Adiciona a configuração do linter estático GitPR para análise de código modificado, prevenindo erros comuns (debug, console.log, localhost hardcoded, rotas não RESTful) e promovendo boas práticas (docblocks e revisão de TODO/FIXME). Também inclui os protótipos iniciais da versão 3 do TechPulse: tela principal, tela de artigo e variações da seção de comentários (busca, filtro, upvotes, compartilhamento e respostas em thread).

🛠️ Mudanças Técnicas
- Cria `.gitpr/skill/.gitpr.linter.yml` com 7 regras configuráveis (nível error/warning, extensões, paths, regex, mensagens dinâmicas).
- Adiciona 5 protótipos em `doc/prototipos/versao_3/prototipos_iniciais/`:
  - `article_detail_with_comments` – página de artigo com discussão e respostas aninhadas.
  - `article_page_with_comment_search` – comentários com campo de busca.
  - `article_page_with_comments_filter` – filtro de ordenação (Novos, Top Comentários).
  - `article_page_with_share_button` – botões de compartilhamento e bookmark em destaque.
  - `article_page_with_upvotes` – contador de upvotes nos comentários.
- Adiciona a área de notícias em `rea_de_not_cias_techpulse`.
- Adiciona a versão final consolidada da tela principal (`tela_principal`) e da tela de artigo (`tela_artigo`) com seção de discussão.

⚠️ Impacto/Avisos
- Nenhuma mudança em banco de dados, variáveis de ambiente ou dependências de runtime.
- A configuração do linter cria uma nova ferramenta de qualidade que roda localmente e pode bloquear commits caso regras de erro sejam violadas.
- Os protótipos são estáticos (HTML/CSS) e não afetam a aplicação em produção.

close #83