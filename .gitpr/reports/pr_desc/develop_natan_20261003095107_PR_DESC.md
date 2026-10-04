# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: reforca sombra do resumo em destaque para legibilidade
```

---

## 🎯 Resumo

O texto de resumo exibido sobre a imagem de destaque perdia legibilidade em cenários de fundo com contraste intermediário: a sombra única aplicada não separava o texto de áreas claras ou escuras da imagem, principalmente em telas menores e com brilho reduzido. Esta mudança reforça a sombra com camadas duplas, garantindo leitura confortável tanto no tema escuro quanto no claro.

## 🛠️ Mudanças Técnicas

- Atualiza a classe computada `resumo_destaque_classe` em `resources/js/Pages/Home.vue`.
- Substitui a sombra única por duas camadas: um halo difuso (`0_0_12px`) para separação do fundo e uma sombra de profundidade (`0_2px_8px`) para definição do contorno.
- Eleva a opacidade das cores de sombra (`rgba(0,0,0,0.9)` no tema escuro e `rgba(255,255,255,0.95)` no tema claro) para reforçar o contraste.
- Mantém intacta a lógica condicional baseada em `destaque_eh_escuro`, sem alteração de comportamento ou de estrutura do componente.
- Atualiza os relatórios de apoio em `.gitpr/reports/pr_desc/` e `docs/claude-code/reports/`.

## ⚠️ Impacto/Avisos

- Sem alteração de banco de dados, variáveis de ambiente ou dependências.
- Mudança restrita a estilo (Tailwind via classes utilitárias), sem impacto em API, rotas ou props consumidas pelo app Flutter.
- Efeito visual percebido apenas no bloco de destaque da Home; recomenda-se conferir em ambos os temas e em telas pequenas antes do merge.
- `text-shadow` é suportado por todos os navegadores alvo; nenhum polyfill necessário.

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)