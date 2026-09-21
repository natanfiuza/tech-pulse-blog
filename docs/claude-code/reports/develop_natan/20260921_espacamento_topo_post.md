# Relatório de Implementação: Espaçamento do Topo do Conteúdo sob a Barra Fixa (Post, Login e Cadastro)

- **Data:** 2026-09-21
- **Branch:** `develop_natan`
- **Tarefa:** Corrigir a sobreposição do conteúdo pela barra superior fixa nas telas públicas de Post, Login e Cadastro, deixando o topo do conteúdo abaixo da barra com o respiro editorial previsto no protótipo v3.

---

## 1. O que foi feito

### 1.1 Diagnóstico

- A barra pública é `fixed top-0 z-50` com container interno `h-20` — **80px**, sem altura responsiva e sem comportamento de scroll ([Navbar.vue](../../../../resources/js/Components/Navbar.vue)).
- O `<body>` não tem padding algum ([app.blade.php](../../../../resources/views/app.blade.php)), então o conteúdo de toda página começa em `y=0`, atrás da barra.
- Cada página pública compensa isso com um literal próprio no `<main>`. Três telas ficaram com `py-12` (48px) e portanto com **32px encobertos**: Post, Login e Cadastro.
- No **Post** o efeito é duplo: a linha de chips (categoria + hashtags) do topo do cabeçalho do artigo fica atrás da barra **em qualquer largura**; e, só a partir do `lg`, o quadro "Tags Relacionadas" sobe para o topo da coluna direita (por causa do `lg:mt-0`) e tem seu topo cortado. **É essa assimetria que fazia o sintoma parecer exclusivo de desktop** — abaixo do `lg` o quadro tem `mt-12` e desce para o fim da página.
- No **Login/Cadastro** o card tinha o topo cortado (borda arredondada + parte do `p-8`) e o `<h1>` caía exatamente em `y=80`, colado na borda inferior da barra.

### 1.2 Critério do valor aplicado

O protótipo do artigo resolve o mesmo problema explicitamente: `pt-20` no `<body>` (80px, sem prefixo de breakpoint) somado ao `py-12` do `<main>` = **128px** do topo da viewport até o cabeçalho. Como os protótipos v3 são a fonte de verdade visual ([ADR-0001](../../../../docs/adr/0001-tailwind-no-projeto-inteiro.md)), o Post recebeu esse mesmo total: `pt-32` (128px) + `pb-12`, preservando os 48px de respiro editorial que o `py-12` já dava e mantendo a base da página inalterada.

### 1.3 Ajustes

| Arquivo | Antes | Depois |
|---|---|---|
| [Post.vue](../../../../resources/js/Pages/Post.vue) | `... px-4 sm:px-8 py-12 lg:grid ...` | `... px-4 sm:px-8 pt-32 pb-12 lg:grid ...` |
| [Auth/Login.vue](../../../../resources/js/Pages/Auth/Login.vue) | `... px-4 sm:px-8 py-12 flex items-start ...` | `... px-4 sm:px-8 pt-28 md:pt-32 pb-12 flex items-start ...` |
| [Auth/Register.vue](../../../../resources/js/Pages/Auth/Register.vue) | `... px-4 sm:px-8 py-12 flex items-start ...` | `... px-4 sm:px-8 pt-28 md:pt-32 pb-12 flex items-start ...` |

- No Post foi adicionado um comentário curto acima do `<main>` — `pt-32 = 80px da barra fixa + 48px de respiro, como no protótipo tela_artigo` — para o 128 não ser "normalizado" para 96 numa passada futura. No Login/Register o valor dispensa comentário: é a mesma combinação já usada em [Tags/Show.vue](../../../../resources/js/Pages/Tags/Show.vue).
- Optou-se por `pt-32 pb-12` em vez de `py-12 pt-32`: o resultado seria idêntico (o Tailwind emite `pt-*` depois de `py-*`), mas a primeira forma não depende da ordem de emissão das utilities.
- Nenhuma mudança em `Navbar.vue` (a altura continua `h-20`), em `tailwind.config.js`, em `resources/css/`, no `CONTEXT.md` ou em ADRs. Não foi criado token nem CSS var: o projeto não tem abstração para a altura da barra e o padrão de fato é o literal por tela — decisão alinhada com a regra do agente TechPulse Layout de não recriar tokens.

### 1.4 Auditoria das outras telas (todas as 20 páginas de `Pages/`)

Folga = topo efetivo do `<main>` − altura da barra (80px público / 64px admin).

| Tela | Topo efetivo | Folga | Veredito |
|---|---|---|---|
| `Post.vue` | 48 → **128** | −32 → **+48** | corrigido |
| `Auth/Login.vue`, `Auth/Register.vue` | 48 → **112/128** | −32 → **+32/+48** | corrigidos |
| `Home.vue` | 96/112 | +16/+32 | ok, não tocado |
| `Tags/Show.vue` | 112/128 | +32/+48 | ok, não tocado |
| `NewsletterConfirmPage.vue`, `NewsletterCancelPage.vue` | 112/144 | +32/+64 | ok, não tocados |
| `Public/Profile.vue`, `Reader/Profile.vue`, `Reader/Dashboard.vue` | 112 | +32 | ok, não tocados |
| 9 páginas `Admin/*` (via `AdminLayout.vue`) | 96 | +32 | ok, não tocados |
| `Errors/404.vue` | 0 | n/a | não renderiza barra |

Com isso, **nenhuma tela pública fica com conteúdo sob a barra**.

---

## 2. Testes e Validações

- **Build do frontend:** `npm run build` executado com sucesso (`✓ built in 42.55s`). O único aviso é o de chunk acima de 500 kB, pré-existente e sem relação com esta mudança.
- **Geração das classes:** confirmado no CSS gerado (`public/build/assets/tailwind-DCfZ5TPZ.css`) que o JIT emitiu as quatro utilities usadas — `.pt-32{padding-top:8rem}`, `.pt-28{padding-top:7rem}`, `.md\:pt-32{padding-top:8rem}` e `.pb-12{padding-bottom:3rem}`.
- **Conferência visual em navegador: NÃO executada.** Não há browser no ambiente de execução e a análise de posição foi feita a partir das classes, não por medição em tela. Checklist para conferência manual (`php artisan serve` + `npm run dev`):
  - Post em largura < 640px: linha de chips (categoria + hashtags) inteiramente visível abaixo da barra.
  - Post em largura ≥ 1024px: título "TAGS RELACIONADAS" visível no topo da coluna direita, com o `aside` ainda alinhado ao topo da coluna esquerda.
  - Post: base da página inalterada (só o padding de topo mudou).
  - Login e Cadastro: borda superior do card e o `<h1>` visíveis, com folga; card continua centralizado na horizontal.
  - Nenhuma das telas com rolagem horizontal.
- **Testes automatizados:** nenhum. A suíte PHPUnit do projeto cobre apenas backend e nenhum arquivo PHP foi tocado.

---

## 3. Arquivos Modificados

- [resources/js/Pages/Post.vue](../../../../resources/js/Pages/Post.vue) — linha 12 (classe do `<main>`) + comentário explicativo.
- [resources/js/Pages/Auth/Login.vue](../../../../resources/js/Pages/Auth/Login.vue) — linha 5.
- [resources/js/Pages/Auth/Register.vue](../../../../resources/js/Pages/Auth/Register.vue) — linha 5.

Artefatos gerados:

- [docs/survey/20260921_espacamento_topo_post_surveyfacts.md](../../../../docs/survey/20260921_espacamento_topo_post_surveyfacts.md) — levantamento completo (rodadas do grill, geometria da barra, auditoria das 20 páginas).

---

## 4. Fora de Escopo — Achados da Auditoria (não corrigidos)

1. **`Errors/404.vue` usa `<inertia-link>`**, componente da era Vue 2, que não está registrado nesta stack (Vue 3 + `Link` de `@inertiajs/vue3`) — renderiza como elemento desconhecido. Bug real e independente; merece task própria.
2. **`Layouts/DashboardResponsivo.vue` é código morto** (zero referências em `resources/js`) e teria overlap de −40px se fosse montado (topbar de 60px contra `padding: 20px`).
3. **Âncora `#newsletter` sem `scroll-margin-top`** — os alvos são `SidebarPanel` em `Post.vue` e `Home.vue`, e nenhum elemento do repo tem `scroll-mt`/`scroll-margin`. Porém os dois links que apontam para ela (`Navbar.vue` e `Footer.vue`) são `<Link>` do Inertia, que não faz scroll para hash nativamente; como não foi possível estabelecer que o defeito sequer se manifesta, não se alterou nada no escuro.

---

## 5. Riscos e Premissas

- **Premissa não medida:** as posições em `y` (chips a ~48–76px, `<h1>` a ~95–100px, `<h1>` do Login a exatamente 80px) foram derivadas das alturas das classes, e não medidas em navegador. Elas explicam o sintoma relatado, mas a conferência visual continua pendente.
- **Divergência de fonte de verdade:** o protótipo da Home não tem o `pt-20` que o do artigo tem, então o próprio conjunto de protótipos é internamente incoerente quanto a esse offset. A implementação seguiu o protótipo do **artigo** para a tela de artigo e a convenção de fato do repo para Login/Cadastro.
- **Duplicação conhecida:** a altura de 80px da barra segue expressa como literal (`h-20`) em `Navbar.vue` e replicada nos paddings de cada tela. Se a altura da barra mudar, todas as telas precisam ser revistas — não há token que propague a mudança. Consolidar isso é uma task à parte.
