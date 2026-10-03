# Relatório de Implementação: Sombra do Excerpt do Artigo em Destaque na Home

- **Data:** 2026-10-03
- **Branch:** `develop_natan`
- **Tarefa:** Ajustar a sombra de texto do resumo/excerpt do artigo em destaque na Home para aplicar a mesma propriedade e intensidade de sombra utilizada no título, adaptando-se dinamicamente ao contraste da imagem de fundo.

---

## 1. O que foi feito

### 1.1 Diagnóstico
- Na tela principal (`Home.vue`), o componente analisa o brilho e contraste da imagem de destaque através do helper `analisar_contraste_imagem()`.
- O título do destaque (`titulo_destaque_classe`) utilizava:
  - Fundo escuro: `text-white [text-shadow:0_2px_10px_rgba(0,0,0,0.85)]`
  - Fundo claro: `text-zinc-950 [text-shadow:0_2px_8px_rgba(255,255,255,0.9)]`
- O resumo/excerpt (`resumo_destaque_classe`) estava com uma sombra mais sutil (`[text-shadow:0_1px_6px_rgba(0,0,0,0.8)]` / `[text-shadow:0_1px_6px_rgba(255,255,255,0.8)]`), o que prejudicava o destaque e legibilidade sobre certas imagens de fundo com variações de luminância.

### 1.2 Ajustes
- Foi atualizada a propriedade computada `resumo_destaque_classe` em [resources/js/Pages/Home.vue](../../../../resources/js/Pages/Home.vue) para alinhar a sombra de texto com a mesma especificação do título:
  - Fundo escuro: `[text-shadow:0_2px_10px_rgba(0,0,0,0.85)]`
  - Fundo claro: `[text-shadow:0_2px_8px_rgba(255,255,255,0.9)]`

| Arquivo | Antes | Depois |
|---|---|---|
| [Home.vue](../../../../resources/js/Pages/Home.vue) | `text-zinc-200 [text-shadow:0_1px_6px_rgba(0,0,0,0.8)]` / `text-zinc-800 [text-shadow:0_1px_6px_rgba(255,255,255,0.8)]` | `text-zinc-200 [text-shadow:0_2px_10px_rgba(0,0,0,0.85)]` / `text-zinc-800 [text-shadow:0_2px_8px_rgba(255,255,255,0.9)]` |

---

## 2. Testes e Validações

- **Build do frontend:** `npm run build` executado com sucesso sem erros (`✓ built in 33.28s`).
- **Validação de regras:** Nenhuma quebra de convenções de nomenclatura ou estilo.
