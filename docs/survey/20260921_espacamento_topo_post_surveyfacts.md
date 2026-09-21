# Survey de Fatos — Espaçamento do Topo do Conteúdo sob a Barra Fixa

**Data:** 21 de setembro de 2026
**Contexto:** Telas públicas de Post, Login e Cadastro (`resources/js/Pages/Post.vue`, `Auth/Login.vue`, `Auth/Register.vue`) e a barra superior fixa compartilhada (`resources/js/Components/Navbar.vue`).
**Pedido original:** "Na tela de visualização do Post, aumente o top do conteúdo para o tamanho da altura da barra superior, pois parte do título e do quadro das tags relacionadas estão por baixo da barra superior."

---

## 1. Problema e Motivação

- A barra superior pública é `fixed top-0`, com 80px de altura, e o `<body>` não tem padding algum. Todo conteúdo de página começa em `y=0`, **atrás** da barra.
- Cada página pública compensa isso com um literal próprio no `<main>` — não existe token, CSS var ou classe compartilhada para a altura da barra. Três telas ficaram com `py-12` (48px) e, portanto, com **32px de conteúdo encoberto**: Post, Login e Cadastro.
- O sintoma relatado (*"parte do título e do quadro das tags relacionadas estão por baixo da barra"*) aparece como exclusivo de desktop por causa do `lg:mt-0` em `Post.vue:118`: só a partir do `lg` o quadro "Tags Relacionadas" sobe para o topo da coluna direita; abaixo disso ele tem `mt-12` e desce para o fim da página. Já a **linha de chips** (categoria + hashtags) que abre o cabeçalho do artigo fica cortada em **qualquer** largura.

---

## 2. Decisões (rodadas do grill)

### Rodada 1 — escopo e abordagem

| # | Decisão | Resposta |
|---|---|---|
| 1 | Onde o problema aparece | "Só no desktop" (observação do usuário) |
| 2 | Escopo | Corrigir o Post e **verificar** as outras telas públicas, reportando antes de mexer |
| 3 | Abordagem | Manter a barra sobrepondo e afastar o conteúdo (não tornar a barra estática) |

### Rodada 2 — valor e breakpoints

| # | Decisão | Por quê |
|---|---|---|
| 4 | Offset do Post = **128px** (`pt-32`) | É o total do protótipo `tela_artigo` (`pt-20` no body + `py-12` no main). Os protótipos v3 são a fonte de verdade visual pelo ADR-0001 |
| 5 | Aplicar em **todos os breakpoints** (sem prefixo) | O código mostra a faixa coberta em qualquer largura: o protótipo usa `pt-20` sem prefixo, e no mobile os chips do topo do artigo também estão atrás da barra |
| 6 | Expressar como **literal no `<main>`** | Padrão já existente nas outras 8 telas; criar abstração para uma tela seria inconsistente, e o agente TechPulse Layout manda não recriar tokens |

### Rodada 3 — telas irmãs encontradas na auditoria

| # | Decisão | Por quê |
|---|---|---|
| 7 | Corrigir **Login e Cadastro** junto, com `pt-28 md:pt-32 pb-12` | Mesmo defeito (−32px), card com o topo cortado e `<h1>` a y=80 colado na barra. O valor é a combinação que `Tags/Show.vue:15` já usa — não introduz número novo no projeto |
| 8 | **Não** tocar nas outras 17 telas | Auditoria mostrou folga positiva em todas (ver §3) |
| 9 | 404 com `<inertia-link>`, `DashboardResponsivo.vue` morto e a âncora `#newsletter` | Registrados como observação, **não** corrigidos: são de outra família ou têm premissa não estabelecida |

---

## 3. Relatório de Fatos Levantados

### Geometria da barra

| Fato | Fonte |
|---|---|
| Barra pública `fixed top-0 w-full z-50`, `glass-header`, container interno **`h-20` (80px)** | `../../resources/js/Components/Navbar.vue:2-5` |
| Altura **não** é responsiva (`h-20` puro, sem `sm:`/`lg:`) e **não** há comportamento de scroll | `Navbar.vue:5`; nenhum `addEventListener('scroll')` em todo o `resources/js` |
| Barra admin é `h-16` (**64px**) | `../../resources/js/Components/Admin/Topbar.vue:2-3` |
| `<body>` sem classe de padding | `../../resources/views/app.blade.php:38` |
| `tailwind.config.js` não estende `spacing`/`height` → escala nativa: `py-12`=48, `pt-20`=80, `pt-24`=96, `pt-28`=112, `pt-32`=128 | `tailwind.config.js` |
| **Não existe** token/CSS var/classe compartilhada para a altura da barra: zero ocorrências de `header-height`, `--header`, `scroll-mt`, `scroll-margin`, `scroll-behavior`, `sticky`, `offsetTop` ou `scrollIntoView` em `resources/` | grep em todo o `resources/` |

### Fonte de verdade visual

- `docs/prototipos/versao_3/tela_artigo/code.html:92` — `<body class="... pt-20 ...">` (80px, sem prefixo) **+** `:116` `<main class="... py-12 ...">` (48px) = **128px** do topo da viewport até o cabeçalho do artigo. É a única documentação do offset que existe no repo.
- Os `DESIGN.md` dos protótipos **não** citam altura de barra, z-index nem offset — a dupla `h-20`/`pt-20` só existe no `code.html`.
- `tela_principal/code.html:88` (Home) **não** tem o `pt-20`, apesar da nav idêntica: incoerência do próprio protótipo, resolvida na implementação com literais diferentes por tela.
- Nenhum ADR cobre offset de header fixo ou z-index. `CONTEXT.md` é glossário de domínio e não tem nada de layout — **nenhum termo novo entra ali** (offset é detalhe de implementação).

### Auditoria das 20 páginas de `resources/js/Pages/`

Folga = topo efetivo − altura da barra (80px público / 64px admin).

| Tela | Topo efetivo (base/md) | Folga | Veredito |
|---|---|---|---|
| `Post.vue:12` | 48 / 48 | **−32** | **sobrepõe** |
| `Auth/Login.vue:5` | 48 / 48 | **−32** | **sobrepõe** |
| `Auth/Register.vue:5` | 48 / 48 | **−32** | **sobrepõe** |
| `Home.vue:7` | 96 / 112 | +16 / +32 | ok |
| `Tags/Show.vue:15` | 112 / 128 | +32 / +48 | ok |
| `NewsletterConfirmPage.vue:5`, `NewsletterCancelPage.vue:5` | 112 / 144 | +32 / +64 | ok |
| `Public/Profile.vue:13`, `Reader/Profile.vue:7`, `Reader/Dashboard.vue:5` | 112 / 112 | +32 | ok |
| 9 páginas `Admin/*` (via `AdminLayout.vue:14`, `pt-24`) | 96 / 96 | +32 (vs 64px) | ok |
| `Errors/404.vue:2` | 0 | n/a | sem barra (fora do escopo) |

- Nenhuma página tem padding no wrapper raiz: o topo efetivo é sempre o do `<main>`/shell. Nenhum offset vem de JS.
- Primeiro elemento afetado: no Post é a linha de chips (`Post.vue:20`, `y=48` a ~76, atrás da barra) — o `<h1>` (`:29`) começa a ~95–100px, ou seja ~15–20px **abaixo** da barra (derivado das classes, não medido no navegador); no Login/Cadastro é o card (`:6-8`), com o `<h1>` (`:14`) exatamente em `y=80`.
- `AdminLayout.vue` é o único shell de admin; nenhuma página admin renderiza a própria barra.

### Órfãos e defeitos adjacentes (não corrigidos)

| Achado | Evidência | Por que ficou de fora |
|---|---|---|
| `Errors/404.vue` usa `<inertia-link>`, componente da era Vue 2, não registrado nesta stack (Vue 3 + `Link` de `@inertiajs/vue3`) → renderiza como elemento desconhecido | `Errors/404.vue:5` | Bug real, de outra família; merece task própria |
| `resources/js/Layouts/DashboardResponsivo.vue` é código morto (zero referências) e teria overlap de −40px (`topbar` 60px vs `padding: 20px`) se fosse montado | `DashboardResponsivo.vue:188` e `:273`; grep sem nenhuma referência | Não está montado |
| Alvos de âncora `#newsletter` sem `scroll-margin-top` | `Post.vue:131`, `Home.vue:92`; links em `Navbar.vue:49` e `Footer.vue:24` | Os dois links são `<Link>` do Inertia, que **não** faz scroll para hash nativamente — não deu para estabelecer que o defeito se manifesta |

---

## 4. O que foi implementado

| Arquivo | Mudança |
|---|---|
| `../../resources/js/Pages/Post.vue:12` | `py-12` → `pt-32 pb-12` (128px), em todos os breakpoints, com comentário curto explicando a soma 80 + 48 para o valor não ser "normalizado" depois |
| `../../resources/js/Pages/Auth/Login.vue:5` | `py-12` → `pt-28 md:pt-32 pb-12` |
| `../../resources/js/Pages/Auth/Register.vue:5` | `py-12` → `pt-28 md:pt-32 pb-12` |

Nenhuma mudança em `Navbar.vue` (a altura continua `h-20`), `tailwind.config.js`, `resources/css/`, `CONTEXT.md` ou ADRs.

**Validação:** `npm run build` passou (42,55s) e as quatro classes novas foram confirmadas no CSS gerado (`public/build/assets/tailwind-DCfZ5TPZ.css`): `.pt-32{padding-top:8rem}`, `.pt-28{padding-top:7rem}`, `.md\:pt-32{padding-top:8rem}`, `.pb-12{padding-bottom:3rem}`. A conferência visual em navegador **não** foi executada por mim (sem browser no ambiente) — checklist no relatório da tarefa.
