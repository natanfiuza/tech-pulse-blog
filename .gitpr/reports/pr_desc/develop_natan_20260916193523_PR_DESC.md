# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: usa origem do navegador como fallback da URL base do Ziggy
```

---

## 🎯 Resumo

Em produção, links gerados pelo Ziggy caíam em `http://localhost:8000` porque o arquivo `resources/js/ziggy.js` trazia a URL base fixa do ambiente local. Como o `window.Ziggy.url` só é injetado em determinados contextos, a navegação e a edição de categorias exibiam endereços inválidos para o usuário final. Este ajuste garante que, na ausência do valor compartilhado pelo servidor, a URL base seja derivada da própria origem do navegador — mantendo os links corretos em qualquer ambiente (local, homologação ou produção).

## 🛠️ Mudanças Técnicas

- Reestrutura o bloco `if (typeof window !== 'undefined')` em `resources/js/ziggy.js` para encadear as verificações de `window.Ziggy` e `window.Ziggy.url` de forma explícita.
- Mantém a precedência do valor vindo do servidor: quando `window.Ziggy.url` existe, `Ziggy.url` e `Ziggy.port` continuam sendo sobrescritos por ele.
- Adiciona fallback para `window.location.origin` quando não há URL compartilhada, definindo `Ziggy.url` com a origem ativa e `Ziggy.port` a partir de `window.location.port` (ou `null` quando a porta é implícita).
- Elimina qualquer dependência residual do valor hardcoded `localhost:8000` para a geração de links.

## ⚠️ Impacto/Avisos

- **Sem alterações de banco de dados, variáveis de ambiente ou dependências.**
- `resources/js/ziggy.js` é um artefato normalmente gerado pelo comando de publicação do Ziggy: mudanças manuais nesse arquivo podem ser sobrescritas em futuras regerações. Recomenda-se avaliar a correção também no template/geração para evitar regressão.
- O fallback por `window.location.origin` assume que o app é servido exatamente na mesma origem das rotas do Laravel; cenários com proxy reverso ou domínios distintos do backend podem exigir configuração explícita de `APP_URL`/`window.Ziggy.url`.
- Requer rebuild dos assets de frontend para entrar em vigor.


close #90