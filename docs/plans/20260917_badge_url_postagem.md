# Plano de Implementação — Cópia de URL na Listagem de Posts

Adicionar exibição do slug/endereço e botão/badge de cópia rápida para a área de transferência na listagem administrativa de posts ([`PostsIndex.vue`](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Pages/Admin/Posts/PostsIndex.vue)), permitindo visualizar e copiar a URL completa gerada tanto para rascunhos quanto para posts publicados/agendados.

## Decisões Alinhadas

- **Opção A selecionada**: Badge interativo com exibição do caminho do slug (`/post/show/{post.slug}`) e ícone de cópia no corpo de cada card de post.
- **Formato da URL**: URL completa e absoluta (`${window.location.origin}/post/show/${post.slug}`).
- **Feedback visual**: Alternância instantânea para ícone de confirmação (`check`) + tag *"Copiado!"* por 2 segundos.
- **Compatibilidade**: Suporte a `navigator.clipboard.writeText` com fallback seguro para navegadores/contextos sem suporte à API moderna.

---

## Modificações Propostas

### Frontend Admin

#### [MODIFY] [PostsIndex.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Pages/Admin/Posts/PostsIndex.vue)

- Adicionar no template de cada card de post o badge interativo contendo:
  - Ícone de cópia (`content_copy` / `check`)
  - Caminho relativo do slug (`/post/show/${post.slug}`) com limite de largura e truncate
  - Indicador visual animado *"Copiado!"*
- Adicionar estado reativo (`uuid_post_copiado`, `timeout_copia`).
- Implementar métodos em estrito `snake_case`:
  - `obter_url_completa(post)`
  - `copiar_url_post(post)`
  - `copiar_fallback(texto, uuid)`
  - `marcar_copiado(uuid)`

---

## Documentação

- Criar survey de fatos: `docs/survey/20260917_copiar_url_post_surveyfacts.md`
- Criar relatório de tarefa: `docs/claude-code/reports/develop_natan/20260917_copiar_url_post_listagem.md`

---

## Plano de Verificação

### Teste de Build
- Executar `npm run build` para garantir ausência de erros de compilação ou sintaxe Vue/Vite.

### Verificação de Convenções
- Verificar ausência de `console.log` e `dd()`.
- Verificar conformidade estrita de nomenclatura `snake_case`.
- Verificar que não há URLs hardcoded (`localhost`/`127.0.0.1`).
