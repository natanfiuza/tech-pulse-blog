# Relatório de Tarefa: Correção da URL de Edição de Categoria em Produção

**Data:** 16/09/2026  
**Branch:** `develop_natan`  
**Autor:** Antigravity / Claude Code

---

## 1. Descrição do Problema

Ao clicar no botão "Editar" de uma categoria em produção (`https://tech-pulse.natanfiuza.dev.br/admin/categories`), o frontend gerava uma requisição para `http://localhost:8000/login` ou `http://localhost:8000/admin/categories/edit/...`, estourando o erro no console:

```text
localhost:8000/login:1 Failed to load resource: net::ERR_CONNECTION_CLOSED
AxiosError: Network Error
```

### Causa Raiz
O arquivo [`resources/js/ziggy.js`](../../../resources/js/ziggy.js) continha o valor inicial `"url": "http://localhost:8000"` compilado diretamente no bundle Vite. A tentativa existente de sobrescrever esse valor através de `window.Ziggy` falhava porque a diretiva `@routes` do Blade emite `const Ziggy = ...`, que não expõe atributos em `window.Ziggy`. Como resultado, qualquer chamada a `route('categories.edit', ...)` montava a URL absoluta apontando para a máquina local (`localhost:8000`).

---

## 2. Solução Implementada

1. **Resolução Dinâmica no [`resources/js/ziggy.js`](../../../resources/js/ziggy.js):**
   - Atualizado o bloco de inicialização no browser (`typeof window !== 'undefined'`) para detectar dinamicamente `window.location.origin` e `window.location.port`.
   - Se `window.Ziggy` existir e tiver `url`, mantém o valor customizado; caso contrário, utiliza a origem do navegador atual (ex.: `https://tech-pulse.natanfiuza.dev.br` em produção e `http://localhost:8000` em ambiente local).
   - O `Ziggy.port` é ajustado para `null` quando não houver porta explícita (padrão 80/443), evitando a anexação indevida de `:8000`.

2. **Recompilação dos Assets (`npm run build`):**
   - Os bundles de produção em `public/build/` foram gerados com a nova lógica dinâmica.

3. **Documentação e Survey:**
   - Registrado o levantamento da sessão de grill em [`docs/survey/20260916_edicao_categoria_url_producao_surveyfacts.md`](../../../docs/survey/20260916_edicao_categoria_url_producao_surveyfacts.md).

---

## 3. Arquivos Modificados e Criados

- [`resources/js/ziggy.js`](../../../resources/js/ziggy.js) — Inclusão de fallback dinâmico baseado em `window.location.origin` e `port`.
- `public/build/*` — Recompilação dos assets Vite para produção.
- [`docs/survey/20260916_edicao_categoria_url_producao_surveyfacts.md`](../../../docs/survey/20260916_edicao_categoria_url_producao_surveyfacts.md) — Documento de fatos levantados da sessão de grill.
- [`docs/claude-code/reports/develop_natan/20260916_correcao_edicao_categoria_url_producao.md`](20260916_correcao_edicao_categoria_url_producao.md) — Relatório de conclusão da tarefa.

---

## 4. Verificação

- `npm run build`: Concluído com sucesso (código 0).
- `vendor/bin/phpunit tests/Unit/ExampleTest.php`: Executado com sucesso (1 teste, 1 asserção).

