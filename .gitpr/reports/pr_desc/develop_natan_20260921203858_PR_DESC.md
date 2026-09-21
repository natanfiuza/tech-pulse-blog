# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: usa url fixa no link da newsletter e regenera ziggy
```

---

## 🎯 Resumo

Os atalhos para a página de newsletter no `Footer.vue` e na `Navbar.vue` dependiam do helper `route('newsletter.page')`, porém as rotas de newsletter não estavam presentes no `ziggy.js` gerado, o que impedia a resolução correta da URL no front-end e arriscava quebrar a renderização/CTA desses componentes. Esta alteração elimina essa dependência nos dois pontos de entrada da newsletter (usando o caminho estático `/newsletter`) e regenera o Ziggy para expor os endpoints de newsletter ao restante da aplicação (formulário, confirmação, cancelamento e descadastro).

## 🛠️ Mudanças Técnicas

- `resources/js/Components/Footer.vue`: link da newsletter alterado de `:href="route('newsletter.page')"` para `href="/newsletter"`.
- `resources/js/Components/Navbar.vue`: mesmo ajuste no botão de CTA do topo.
- `resources/js/ziggy.js`: arquivo regenerado, agora contendo as rotas `newsletter.page`, `newsletter.subscribe`, `newsletter.send-cancel-link`, `newsletter.confirm`, `newsletter.confirm.submit`, `newsletter.cancel` e `newsletter.unsubscribe`.
- Documentação de apoio atualizada (`docs/claude-code/reports/...` e relatórios de linter/PR desc).

## ⚠️ Impacto/Avisos

- **Banco de dados / Envs / Dependências:** nenhuma migração, variável de ambiente ou dependência nova.
- **Rotas hardcoded:** o uso de `/newsletter` fixo quebra se a aplicação for servida em subdiretório ou com prefixo de idioma diferente da raiz. Validar em produção antes do deploy.
- **Ziggy é artefato gerado:** não editar manualmente; regenerar (`php artisan ziggy:generate` ou comando equivalente do projeto) sempre que as rotas Laravel mudarem, sob risco de divergência entre back-end e front-end.
- **Consistência:** como o Ziggy agora expõe as rotas de newsletter, avaliar a volta do helper `route('newsletter.page')` nos componentes para manter o padrão do projeto e evitar strings mágicas duplicadas.

---

[![GitPR](https://img.shields.io/badge/GitPR-1_error_%C2%B7_0_warnings-red)](https://gitpr.natanfiuza.dev.br/)