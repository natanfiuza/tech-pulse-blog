# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: ajusta contraste da sombra do resumo em destaque
```

---

## 🎯 Resumo

Ajusta a intensidade e o desfoque da sombra de texto aplicada ao resumo em destaque na home. O objetivo é melhorar a legibilidade do trecho sobre imagens de fundo, garantindo contraste adequado tanto em cenários claros quanto escuros sem alterar a hierarquia visual da página.

## 🛠️ Mudanças Técnicas

- Atualiza o valor de `text-shadow` no modo escuro de `0_1px_6px_rgba(0,0,0,0.8)` para `0_2px_10px_rgba(0,0,0,0.85)`.
- Atualiza o valor de `text-shadow` no modo claro de `0_1px_6px_rgba(255,255,255,0.8)` para `0_2px_8px_rgba(255,255,255,0.9)`.
- Mantém a lógica condicional já existente em `resumo_destaque_classe`, alterando apenas as classes utilitárias do Tailwind.

## ⚠️ Impacto/Avisos

- **Banco de dados:** sem alterações de schema, migrations ou seeds.
- **Variáveis de ambiente:** nenhuma adição, remoção ou renomeação de envs.
- **Dependências:** nenhuma dependência adicionada, atualizada ou removida.
- **Impacto visual:** mudança puramente estética, restrita ao componente `Home.vue`; não afeta API pública, consumo pelo app Flutter ou dados renderizados pelo Inertia.

close #142

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)