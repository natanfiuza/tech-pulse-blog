# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona copia rapida da URL do post na listagem admin
```

---

## 🎯 Resumo

Facilitar o compartilhamento e a verificação de links de publicações diretamente da listagem administrativa. Antes, para obter o endereço público de um post, era necessário abrir a página ou montar a URL manualmente a partir do slug. Agora cada item da listagem exibe um badge com o caminho `/post/show/{slug}` e um botão que copia a URL absoluta em um clique, com confirmação visual imediata.

## 🛠️ Mudanças Técnicas

- Adiciona badge clicável em `PostsIndex.vue` exibindo o slug do post e ícone de cópia (`content_copy`), com truncamento responsivo para slugs longos.
- Implementa `obter_url_completa(post)` para compor a URL absoluta usando `window.location.origin`, com guarda para SSR e para posts sem slug.
- Implementa `copiar_url_post(post)` priorizando a Clipboard API (`navigator.clipboard` + `window.isSecureContext`).
- Implementa `copiar_fallback(texto, uuid)` com `textarea` temporário e `document.execCommand('copy')` para contextos não seguros ou navegadores sem suporte.
- Adiciona estado reativo `uuid_post_copiado` e `timeout_copia` para feedback de sucesso (ícone `check`, selo "Copiado!" e cor esmeralda) com reset automático em 2 segundos.
- Garante limpeza do timeout anterior ao copiar um novo item, evitando que o feedback de um post apague o de outro.

## ⚠️ Impacto/Avisos

- **Sem alterações em banco de dados, rotas, variáveis de ambiente ou dependências.** Mudança restrita ao frontend administrativo.
- A cópia via `navigator.clipboard` exige contexto seguro (HTTPS ou `localhost`); em HTTP o fallback legado é acionado automaticamente.
- A URL gerada depende de `window.location.origin`, portanto reflete o domínio acessado pelo usuário (útil em ambientes de staging, mas requer atenção se houver domínio canônico distinto).
- `document.execCommand` é uma API depreciada; mantida apenas como caminho de compatibilidade e candidata a remoção futura.