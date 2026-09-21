# Ajuste do topo do conteúdo sob a barra fixa — Post, Login e Cadastro

## Context

A barra superior pública é `fixed top-0 z-50` com container interno `h-20` = **80px**, sem altura
responsiva e sem comportamento de scroll ([Navbar.vue:2-5](resources/js/Components/Navbar.vue#L2-L5)),
e o `<body>` não tem padding algum ([app.blade.php:38](resources/views/app.blade.php#L38)). Portanto
qualquer página cujo `<main>` tenha menos de 80px de topo tem os primeiros pixels de conteúdo
atrás da barra.

Três telas usam `py-12` (48px) no `<main>` e ficam com **32px de conteúdo encoberto**:

- **Post** ([Post.vue:12](resources/js/Pages/Post.vue#L12)) — a linha de chips (categoria + hashtags)
  que abre o cabeçalho do artigo fica inteiramente atrás da barra **em qualquer largura**; e, só a
  partir do `lg`, o quadro "Tags Relacionadas" sobe para o topo da coluna direita (por causa do
  `lg:mt-0` em [Post.vue:118](resources/js/Pages/Post.vue#L118)) e tem seu topo cortado. No mobile o
  quadro tem `mt-12` e desce para o fim da página — é exatamente por isso que o sintoma *parece*
  exclusivo de desktop.
- **Login** ([Login.vue:5](resources/js/Pages/Auth/Login.vue#L5)) e **Cadastro**
  ([Register.vue:5](resources/js/Pages/Auth/Register.vue#L5)) — o topo do card (borda arredondada +
  parte do `p-8`) é cortado e o `<h1>` ("Bem-vindo de Volta" / "Criar Conta") cai em y=80, colado na
  borda inferior da barra.

Resultado esperado: o conteúdo das três telas começa abaixo da barra, com o mesmo respiro editorial
que o protótipo do artigo especifica.

**Fonte de verdade visual:** `docs/prototipos/versao_3/tela_artigo/code.html:92` resolve exatamente
este problema com `<body class="... pt-20 ...">` (80px, sem prefixo de breakpoint) + `py-12` no
`<main>` = **128px** do topo da viewport até o cabeçalho do artigo. Segundo o
[ADR-0001](docs/adr/0001-tailwind-no-projeto-inteiro.md), os protótipos v3 são a referência visual do
projeto.

## Decisões fechadas no grill

| #   | Decisão                 | Escolha                                                                      |
| --- | ----------------------- | ---------------------------------------------------------------------------- |
| 1   | Valor do offset no Post | **128px** — igual ao protótipo (`pt-32` + `pb-12`)                           |
| 2   | Breakpoints             | **Todos** (classes sem prefixo, como no protótipo)                           |
| 3   | Onde expressar          | Literal no `<main>`, seguindo o padrão das outras 8 telas do repo            |
| 4   | Login/Cadastro          | **Corrigir junto**, com o padrão das telas internas (`pt-28 md:pt-32 pb-12`) |
| 5   | Demais telas auditadas  | Nenhuma outra está quebrada — não são tocadas                                |

Justificativa do item 3: o repo **não tem** token, CSS var ou classe compartilhada para a altura da
barra — cada página repete o literal no `<main>` (Home `pt-24 md:pt-28`, Tags `pt-28 md:pt-32`,
Profile/Dashboard `py-28`). Criar uma abstração para uma única tela seria inconsistente, e o agente
**TechPulse Layout** manda não recriar tokens. Não há mudança em `tailwind.config.js`,
`resources/css/`, em `Navbar.vue` (a altura continua `h-20`) nem em `CONTEXT.md` (o glossário é
só de domínio; offset é detalhe de implementação). Também **não** cabe ADR: a decisão é facilmente
reversível, não surpreende ninguém e não é fruto de trade-off relevante.

## Mudanças

### 1. `resources/js/Pages/Post.vue` (linha 12)

Trocar `py-12` por `pt-32 pb-12` (128px no topo = 80px da barra + os 48px de respiro que o `py-12`
já dava; a base continua 48px):

```html
<!-- antes -->
<main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-8 py-12 lg:grid lg:grid-cols-12 lg:gap-12">

<!-- depois -->
<main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-8 pt-32 pb-12 lg:grid lg:grid-cols-12 lg:gap-12">
```

Acima do `<main>`, comentário curto em pt-BR no estilo dos que já existem no arquivo
(`<!-- Cabeçalho do artigo -->`, `<!-- Sidebar -->`), para o 128 não ser "normalizado" para 96:

```html
<!-- pt-32 = 80px da barra fixa + 48px de respiro, como no protótipo tela_artigo -->
```

Não usar `py-12 pt-32` no mesmo atributo: embora o Tailwind v3 emita `pt-*` depois de `py-*` e o
resultado fosse o mesmo, `pt-32 pb-12` não depende de ordem de emissão e é inequívoco.

### 2. `resources/js/Pages/Auth/Login.vue` (linha 5) e `resources/js/Pages/Auth/Register.vue` (linha 5)

Trocar `py-12` por `pt-28 md:pt-32 pb-12` (112px no mobile → 32px de folga; 128px a partir do `md` →
48px de folga). É a **mesma combinação já usada** em
[Tags/Show.vue:15](resources/js/Pages/Tags/Show.vue#L15), então não introduz valor novo no projeto; o
`pb-12` preserva a base que o `py-12` dava. Sem comentário aqui — o valor já tem precedente no repo.

```html
<!-- antes (idêntico nos dois arquivos) -->
<main class="flex-grow w-full max-w-md mx-auto px-4 sm:px-8 py-12 flex items-start justify-center">

<!-- depois -->
<main class="flex-grow w-full max-w-md mx-auto px-4 sm:px-8 pt-28 md:pt-32 pb-12 flex items-start justify-center">
```

Nenhum identificador JS é criado ou renomeado (regra `snake_case` do CLAUDE.md não é afetada).

## Fora de escopo — encontrados na auditoria, **não** corrigidos

Registrar no relatório, sem tocar no código:

1. [Errors/404.vue](resources/js/Pages/Errors/404.vue) usa `<inertia-link>`, componente da era Vue 2;
   nesta stack (Vue 3 + `Link` de `@inertiajs/vue3`) ele não está registrado e renderiza como
   elemento desconhecido. Bug real, de outra família — merece task própria.
2. [DashboardResponsivo.vue](resources/js/Layouts/DashboardResponsivo.vue) é código morto (zero
   referências no `resources/js`) e teria overlap de −40px se fosse montado.
3. Âncoras: `#newsletter` ([Post.vue:131](resources/js/Pages/Post.vue#L131),
   [Home.vue:92](resources/js/Pages/Home.vue#L92)) não tem `scroll-margin-top` em lugar nenhum do
   repo, mas os dois links que apontam para ela ([Navbar.vue:49](resources/js/Components/Navbar.vue#L49)
   e [Footer.vue:24](resources/js/Components/Footer.vue#L24)) são `<Link>` do Inertia, que **não**
   faz scroll para hash nativamente. Como não deu para estabelecer que o defeito se manifesta, não
   se mexe no escuro.

## Verificação

1. `npm run build` — precisa passar. É também o que garante que as novas classes (`pt-32`,
   `pt-28 md:pt-32`, `pb-12`) foram geradas pelo JIT: Post.vue, Login.vue e Register.vue entram na
   varredura do Tailwind, e os três utilitários são da escala nativa (sem extensão em
   `tailwind.config.js`).
2. Conferência visual no navegador (`php artisan serve` + `npm run dev`), pelo checklist do agente
   TechPulse Layout — sem overflow horizontal, **sem título cortado**, sem sobreposição:
   - **Post, largura < 640px**: linha de chips (categoria + hashtags) inteiramente visível abaixo da
     barra; título livre.
   - **Post, largura ≥ 1024px**: o título "TAGS RELACIONADAS" no topo da coluna direita visível, sem
     nada atrás da barra; `aside` continua alinhado ao topo da coluna esquerda (`lg:mt-0`).
   - **Post, rodapé**: a base da página inalterada (só o `pt` mudou).
   - **Login e Cadastro**: borda superior do card e o `<h1>` visíveis, com folga; card continua
     centralizado na horizontal.
3. Não há teste automatizado de layout no projeto (a suíte PHPUnit é de backend e nada de PHP é
   tocado por este ajuste) — não rodar `phpunit` e não inventar teste para isto.
4. Se quiser, subo o app com a skill `/run` e confiro visualmente, em vez de deixar a conferência
   só com você.

## Artefatos a gerar na implementação

- **Survey** (exigido pela skill `grill-with-docs`): `docs/survey/20260921_espacamento_topo_post_surveyfacts.md`
  com o contexto da tarefa, as decisões das rodadas e o relatório de fatos levantados (geometria da
  barra, tabela da auditoria das 20 páginas, o que ficou fora de escopo).
- **Relatório** (regra do CLAUDE.md): `docs/claude-code/reports/develop_natan/20260921_espacamento_topo_post.md`,
  no formato dos relatórios existentes (`# Relatório ...`, `**Data:**`, `**Branch:**`, `**Tarefa:**`),
  com caminhos relativos ao diretório do relatório — inclusive o resumo da auditoria e as três
  observações fora de escopo.
