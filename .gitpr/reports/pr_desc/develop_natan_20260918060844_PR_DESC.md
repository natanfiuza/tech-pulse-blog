# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona busca em posts e categorias no painel admin
```

---

## 🎯 Resumo

Os administradores precisavam localizar posts e categorias rapidamente, mas as listagens do painel não ofereciam nenhum mecanismo de filtragem, obrigando a rolagem manual e a leitura visual de todos os registros. Esta mudança habilita uma busca única no topo do painel, contextualizada por tela, que filtra posts (título, resumo, conteúdo e hashtags) e categorias (nome) em tempo real, sem round-trip ao servidor.

## 🛠️ Mudanças Técnicas

- Cria o composable `use_admin_busca.js` com estado reativo compartilhado (`termo_busca`) entre a topbar e as páginas, expondo `limpar_busca`, `filtrar_posts` e `filtrar_categorias`.
- Adiciona o helper `normalizar_texto` em `helpers.js`, removendo acentuação (NFD + strip de diacríticos), convertendo para minúsculas e aplicando `trim`, tornando a busca insensível a caixa e acentos.
- Liga o input de busca da `Topbar.vue` ao estado do composable via `v-model` e define placeholder dinâmico conforme o componente Inertia ativo (`Buscar posts...`, `Buscar categorias...`, fallback `Buscar...`).
- Atualiza `PostsIndex.vue` e `CategoriesIndex.vue` para renderizar `posts_filtrados` / `categories_filtradas` (via `computed`) e exibir mensagem de vazio contextual incluindo o termo pesquisado.
- Ajusta `AdminLayout.vue` para limpar o termo de busca ao navegar entre páginas, evitando que um filtro antigo "vaze" para outra listagem.
- A busca em posts cobre `title`, `excerpt`, `content` e `hashtags` (nome/slug); em categorias, apenas `name`.

## ⚠️ Impacto/Avisos

- **Banco de dados:** nenhuma migration, alteração de schema ou seed. Mudança exclusivamente de frontend.
- **Envs/Dependências:** nenhuma variável de ambiente nova e nenhum pacote adicionado.
- **Comportamento:** a filtragem é client-side e atua somente sobre os registros já carregados pela paginação do Inertia. Em páginas com muitos registros, resultados fora da página atual não serão encontrados — caso esse cenário ocorra, será necessário evoluir para busca server-side.
- **Performance:** a filtragem em `content` percorre o corpo completo dos posts a cada tecla digitada; aceitável no volume atual, mas candidata a debounce ou indexação caso o dataset cresça.

close #100