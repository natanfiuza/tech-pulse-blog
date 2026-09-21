# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige contraste de cards do admin no tema claro
```

---

## 🎯 Resumo

Os cards e painéis do Admin exibiam fundos translúcidos (`bg-surface-container-*` com opacidade) que, no tema claro, resultavam em baixo contraste e aparência "lavada", prejudicando a leitura de métricas e listas. Esta mudança garante fundo branco sólido no modo claro (mantendo os tokens de superfície no modo escuro), adiciona sombras sutis para dar profundidade e assegura que variações de opacidade dos tokens também sejam sobrescritas no tema claro.

## 🛠️ Mudanças Técnicas

- `resources/css/tailwind.css`: estende as regras de override do tema claro (`html:not(.dark)`) para seletores com modificador de opacidade (`[class*="bg-surface-container-high/"]` e `[class*="bg-surface-container-highest/"]`), aplicando `background-color: #ffffff !important`.
- `resources/js/Pages/Admin/AdminHome.vue`: cards de status, lista de posts e ações rápidas passam a usar `bg-white dark:bg-surface-container-*`, com `shadow-sm dark:shadow-none`; hover ajustado para `hover:bg-slate-50 dark:hover:bg-surface-container-high`.
- `resources/js/Components/Admin/VisualizacoesChart.vue`: blocos de KPI ("Total no período" e "Média diária") migrados para `bg-white dark:bg-surface-container-high/70` com sombra apenas no tema claro.
- `resources/js/Components/Admin/Topbar.vue`: título reduzido de "TechPulse Admin" para "Admin" (layout/identidade visual) e atributos quebrados em múltiplas linhas.

## ⚠️ Impacto/Avisos

- **Banco de dados:** nenhuma migração ou alteração de schema.
- **Variáveis de ambiente:** nenhuma adição ou remoção.
- **Dependências:** nenhuma nova dependência adicionada ou atualizada.
- **Mudança visual (breaking estético):** o uso de `!important` no CSS sobrescreve os tokens de superfície no tema claro; novos componentes que dependam de fundos translúcidos no modo claro precisarão de tratamento explícito.
- **Branding:** o rótulo da Topbar mudou de "TechPulse Admin" para "Admin" — confirmar se a alteração é intencional para todos os contextos de navegação.


close #112

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)