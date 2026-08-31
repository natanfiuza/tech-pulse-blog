# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona categorias, hashtags, comentários e tema Tailwind
```

---

🎯 **Resumo**

Esta PR moderniza o TechPulse Blog em três frentes principais: **organização de conteúdo** (categorias, hashtags e status de publicação), **engajamento** (comentários em árvore com upvotes) e **experiência visual** (migração completa de Bootstrap/Sass para Tailwind CSS com o tema dark "Midnight Pulse"). O objetivo é preparar o blog para uma gestão editorial mais rica e uma leitura mais imersiva, mantendo a API pública compatível com o app Flutter.

🛠️ **Mudanças Técnicas**

- Migração do frontend de Bootstrap/Sass para **Tailwind CSS v3** com tema dark "Midnight Pulse"; remoção de dependências legadas (`bootstrap`, `sass-embedded`, `@inertiajs/inertia`) e adoção de `@inertiajs/vue3` unificado.
- **Posts** agora possuem `category_id`, `status` (`rascunho`, `publicado`, `agendado`), `published_at` e hashtags com sincronização automática (firstOrCreate + sync).
- **Home** com filtro por categoria (incluindo subcategorias via BFS), destaque automático do post mais recente, chips de categorias, tags populares, newsletter e rodapé.
- **Página de artigo** com pipeline markdown unificado (`markdown-it` + `highlight.js` tema Night Owl + Mermaid), cabeçalho com metadados, sidebar e seção de comentários.
- **Comentários** em árvore (`parent_id`) com votos alternáveis, permissão para exclusão pelo dono e formulários com validação; apenas posts publicados aceitam comentários.
- **Hashtags** com modelo, tabela pivô `post_hashtag`, página pública `/tags/{slug}` e componente `TagInput` com sugestões.
- **Admin** redesenhado no tema dark: `AdminLayout`, `Sidebar`, `Topbar`, páginas de Posts/Categorias, `ImageDropzone`, `MarkdownEditor` customizado e estados de status.
- **Correção do helper `criar_slug`**: transliteração (`Str::ascii`) antes da remoção de stopwords, limpeza de hífens nas pontas e fallback para títulos vazios; novo comando `app:regenerar-slugs` para reparar slugs quebrados existentes.
- **Rotas corrigidas**: `posts.destroy` agora chama o método correto; rotas de categorias usam binding por `id`; adicionadas rotas de comentários, tags e `posts.show` por `uuid`.
- **API/RSS** passa a retornar apenas posts publicados, agora com `category`, `hashtags` e `published_at`.
- Renomeação da pasta `doc/` para `docs/` e inclusão de protótipos da versão 3.

⚠️ **Impacto/Avisos**

- **Banco de dados**: rodar `php artisan migrate` (5 novas migrações). O backfill define posts existentes como `status = publicado` e `published_at = created_at`.
- **Dependências**: execute `npm install` e `npm run build`. Bootstrap/sass-embedded foram removidos; Tailwind, PostCSS e Autoprefixer foram adicionados.
- **Slugs**: após o deploy, recomenda-se executar `php artisan app:regenerar-slugs` para corrigir slugs antigos que contenham acentos, hífens duplicados ou hífens nas extremidades.
- **Frontend**: o layout público e administrativo foi completamente substituído pelo tema dark; é necessária uma verificação visual das páginas existentes.
- **Rotas**: o frontend deve usar apenas `@inertiajs/vue3`; referências antigas a `post/show/fix/{uuid}` foram mantidas para compatibilidade.

close #85