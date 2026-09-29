# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige duplicações em categorias e ajusta timezone
```

---

## 🎯 Resumo
Corrige artefatos de merge e duplicações nos recursos de categorias, removendo consultas, variáveis e opções repetidas que causavam código PHP inválido e dropdowns duplicados. Também altera o fuso horário padrão da aplicação para America/Recife.

## 🛠️ Mudanças Técnicas
- Remove consultas e variáveis duplicadas no `CategoryController` (`$categories` e `$available_parents`), eliminando chaves repetidas no retorno Inertia e linhas inválidas no método `edit`.
- Remove loops de opções planas duplicadas nos componentes Vue `CategoriesCreate.vue` e `CategoriesEdit.vue`, mantendo apenas o dropdown hierárquico.
- Atualiza `config/app.php` alterando `timezone` de `UTC` para `America/Recife`.
- Limpa asserções duplicadas e adiciona validações de renderização Markdown no teste `ModalCategoriasGuia.spec.js`.

## ⚠️ Impacto/Avisos
- O fuso horário da aplicação muda de `UTC` para `America/Recife`; isso pode afetar exibição de datas, timestamps, logs e agendamentos. Verifique alinhamento com o banco de dados e servidores.
- Não há novas migrations, dependências ou variáveis de ambiente. A alteração de timezone é feita no arquivo de configuração versionado.


close #138

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)