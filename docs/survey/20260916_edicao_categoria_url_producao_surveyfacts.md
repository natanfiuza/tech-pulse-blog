# Survey — Correção da URL de Edição de Categoria em Produção (2026-09-16)

> Levantamento automático da sessão de grill (`/grill-with-docs`) — fronteira esgotada e entendimento compartilhado confirmado pelo usuário.

## 1. Contexto da tarefa
- **Pedido original:** Corriga a edição de categoria, quando clica em editar exibe o erro `localhost:8000/login:1 Failed to load resource: net::ERR_CONNECTION_CLOSED`, `AxiosError: Network Error` ao acessar em produção `https://tech-pulse.natanfiuza.dev.br`.
- **Tela / Módulo:** Painel Admin — Gestão de Categorias (`/admin/categories`, `Admin/Categories/CategoriesIndex.vue`, `Admin/Categories/CategoriesEdit.vue`, `CategoryController`).
- **Skill:** `/grill-with-docs` (grilling + domain-modeling) · branch `develop_natan`
- **Método:** 1 rodada de grill, 2 decisões de engenharia, inspeção detalhada do runtime do Ziggy e do bundle Vite.
- **Arquivos-chave lidos:**
  - `resources/js/ziggy.js`
  - `resources/js/app.js`
  - `resources/views/app.blade.php`
  - `resources/js/Pages/Admin/Categories/CategoriesIndex.vue`
  - `resources/js/Pages/Admin/Categories/CategoriesEdit.vue`
  - `routes/web.php`
  - `app/Http/Controllers/CategoryController.php`

## 2. Decisões das rodadas

### Rodada 1 — Resolução do Ziggy e Rotas do Frontend
| # | Decisão | Resolução |
|---|---|---|
| Q1 | Estratégia de resolução de origem no `ziggy.js` | **Configurar `Ziggy.url` e `Ziggy.port` dinamicamente a partir de `window.location.origin` (com fallback para `window.Ziggy` se injetado)** |
| Q2 | Padronização dos links de navegação da listagem de categorias | **Manter rotas nomeadas com `route('categories.edit', ...)` no padrão do repositório** |

### Defaults assumidos (não contestados)
- As convenções de nomenclatura `snake_case` e proibições de `console.log` e `localhost` hardcoded em URLs permanecem estritamente respeitadas.
- O build de produção deve ser reconstruído (`npm run build`) para atualizar os assets compilados em `public/build/`.

## 3. Relatório de fatos levantados

### Causa Raiz do Erro de Conexão em Produção
O arquivo `resources/js/ziggy.js` continha o valor inicial `"url": "http://localhost:8000"` compilado no bundle do Vite (`app-BMVN8zPk.js`). A tentativa anterior de ler `window.Ziggy` falhava silenciosamente porque a diretiva `@routes` do Blade emite `const Ziggy = ...`, o que não cria a propriedade no objeto global `window`. Quando o usuário clicava em "Editar", a função `route('categories.edit', ...)` montava a URL apontando para `http://localhost:8000/admin/categories/edit/...`, fazendo o browser falhar ao disparar a requisição XHR para uma porta local fechada.

### Comportamento Dinâmico de Resolução
Ao checar `window.location.origin` e `window.location.port` em tempo de execução no cliente, qualquer chamada a `route(...)` resolve para o domínio exato em que a aplicação está rodando (seja produção HTTPS ou ambiente de desenvolvimento local).

