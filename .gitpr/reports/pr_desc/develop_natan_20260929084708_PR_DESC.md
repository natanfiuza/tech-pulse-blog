# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adapta contraste do destaque conforme a imagem
```

---

## 🎯 Resumo

O texto sobreposto ao post em destaque na Home era fixo em branco, o que o tornava ilegível quando a imagem de capa era clara (fundos brancos, fotos estouradas ou com muito céu). Este PR torna o hero auto-adaptativo: a luminância da região inferior da imagem é medida em runtime e o esquema de cores (título, resumo, tempo de leitura e gradiente de overlay) alterna automaticamente entre claro e escuro, preservando a legibilidade e a identidade visual do blog em qualquer capa publicada pela API/Flutter.

## 🛠️ Mudanças Técnicas

- Adiciona o helper `analisar_contraste_imagem` em `resources/js/helpers.js`, que desenha a imagem em um `<canvas>` reduzido a 100x100, amostra a região inferior configurável (`amostra_inicio_y`, padrão 0.4) e calcula a luminância perceptiva pela fórmula ITU-R BT.601 (`0.299R + 0.587G + 0.114B`), retornando `{ eh_escura, luminancia, r, g, b }`.
- Implementa fallback seguro (`eh_escura: true`) para imagem nula, elemento inválido, erro de carregamento, contexto 2D indisponível ou ausência de `window` (compatibilidade SSR/testes).
- Suporta tanto `HTMLImageElement` (inclusive aguardando o evento `load` quando a imagem ainda não está completa) quanto URL em string, carregando uma `Image` com `crossOrigin = "anonymous"`.
- Em `resources/js/Pages/Home.vue`, adiciona o estado reativo `destaque_eh_escuro` e um `watch` com `immediate: true` sobre a URL da imagem do post em destaque, recalculando o contraste sempre que a capa muda.
- Liga o `@load` da imagem do destaque a `ao_carregar_imagem_destaque`, garantindo medição sobre pixels já decodificados (evita leitura de imagem incompleta em cache frio).
- Substitui classes fixas por computadas derivadas: `titulo_destaque_classe`, `resumo_destaque_classe`, `tempo_leitura_classe` e `overlay_gradiente_classe`, com transições de 300–500ms para evitar "flash" de cor.
- O overlay deixa de ser um gradiente `from-surface-dim` fixo e passa a alternar entre `from-black/80` (imagem escura) e `from-white/80` (imagem clara), com textos em `text-white`/`text-zinc-950` e `text-shadow` invertido para reforçar contraste.
- Aplica normalização de formatação (Prettier) nos computeds `featured_post`, `posts_grade` e `tags_populares`.
- Adiciona `tests/Unit/Helpers.spec.js` com Vitest cobrindo `normalizar_texto`, `tempo_leitura`, `url_da_imagem`, `normalizar_origem_conteudo` e `analisar_contraste_imagem` (casos de fallback, imagem escura e imagem clara via mock de `CanvasRenderingContext2D`).

## ⚠️ Impacto/Avisos

- **Sem alterações de banco de dados, migrations, variáveis de ambiente ou dependências** — `vitest` já era usado no projeto e nenhum pacote novo foi instalado.
- **CORS:** imagens externas (ex.: URLs absolutas de CDN) só podem ser lidas pelo canvas se servirem cabeçalhos CORS adequados; caso contrário, o `getImageData` lança exceção e o componente cai no fallback escuro, mantendo o comportamento anterior (texto branco).
- **Custo de renderização:** a análise é feita uma vez por troca de destaque, sobre uma amostra de 100x100 com leitura de 1 a cada 4 pixels, com impacto desprezível em desktop e mobile; ainda assim, é trabalho síncrono após o decode da imagem.
- **Ponto de revisão:** o `watch` referencia o helper `imagem_do_post`, que deve estar acessível no escopo do `<script setup>` do `Home.vue` para o auto-refresh do contraste funcionar em navegação Inertia.
- A verificação de contraste usa o limiar de luminância 128; imagens em tons médios (zona cinzenta) podem alternar de esquema entre capas visualmente semelhantes.


close #140

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)