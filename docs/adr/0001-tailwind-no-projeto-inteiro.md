# Tailwind CSS no projeto inteiro (substituindo Bootstrap)

Na migração para o layout v3 ("Midnight Pulse"), decidimos adotar **Tailwind CSS em todo o projeto** — páginas públicas (Home, artigo) e admin — substituindo gradualmente o Bootstrap 5, que sai ao final da migração. Os protótipos v3 (`tela_principal`, `tela_artigo`, `tela_admin`) são a fonte de verdade visual e são construídos 100% em Tailwind (via CDN, com tokens Material Design 3 dark); traduzir esse design para Bootstrap resultaria em CSS custom massivo e divergência visual garantida em relação aos protótipos.

## Considered Options

- **Tailwind só nas páginas públicas, Bootstrap no admin** (recomendação inicial) — rejeitado pelo usuário (Q4→3): admin também seria redesenhado para o tema v3, então manter Bootstrap lá só preservaria o trabalho de migração para depois.
- **Traduzir tudo para Bootstrap + CSS custom** — rejeitado: esforço grande e risco alto de divergir dos protótipos (classes utilitárias, design tokens, dark theme).
- **Tailwind em todo o projeto** — **escolhido**: migração progressiva (públicas → admin), Bootstrap removido ao final (Q19).

## Consequences

- Convivência temporária de Tailwind e Bootstrap durante a transição (migração progressiva).
- Vite precisa do plugin Tailwind; tokens M3 dark compartilhados em `tailwind.config`.
- `resources/sass/custom.scss`, `resources/css/home.css` e `article.css` serão absorvidos/removidos conforme as páginas migram.
- **Tailwind v3.4** (não v4): os protótipos usam sintaxe v3 (config JS + `darkMode: 'class'`); v4 (CSS-first) quebraria a tradução direta dos tokens.
- **`corePlugins.preflight` desligado** durante a transição — o reset do Tailwind quebraria o admin Bootstrap ainda vivo; reativado na Fase 5 (remoção do Bootstrap).
- Fontes trocadas em `resources/views/app.blade.php`: Inter + JetBrains Mono + Material Symbols Outlined (substituem Fira Code/Libre Franklin/Space Grotesk); `<html class="dark">` fixo.
- Imports Inertia migrados para `@inertiajs/vue3` (v2) antes de os agentes de layout tocarem nas páginas; pacotes `@inertiajs/inertia` e `@inertiajs/inertia-vue3` permanecem até a remoção final.
