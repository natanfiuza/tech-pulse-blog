# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona dropdown hierárquico de categorias no admin
```

---

## 🎯 Resumo

O seletor de categoria pai no admin exibia uma lista plana com todas as categorias, o que dificultava identificar a relação entre categorias e aumentar o risco de escolhas incorretas ao criar ou editar registros. Esta mudança passa a entregar a árvore de categorias (até 3 níveis) ao frontend e a renderiza com recuo visual, tornando a hierarquia legível e reduzindo erros de cadastro.

## 🛠️ Mudanças Técnicas

- `CategoryController@create`: passa a buscar apenas categorias raiz (`parent_id` nulo) com eager loading de `children.children`, ordenadas por nome, montando a árvore de até 3 níveis.
- `CategoryController@edit`: aplica o mesmo carregamento hierárquico e exclui a categoria editada em todos os níveis (raiz, filhos e netos), impedindo que uma categoria seja pai de si mesma.
- Removido o `select(['id', 'name'])` das consultas, que agora carregam o modelo completo.
- `CategoriesCreate.vue`: novo computed `categorias_planas` que achata a árvore em uma lista com `nivel` (0, 1, 2); o `<option>` aplica recuo com espaços não separáveis (`\u00a0`) e negrito para categorias raiz.
- `tests/Components/Admin/ModalCategoriasGuia.spec.js`: expectativa de tags `<strong>` ajustada de 10 para 8 e verificações de conversão markdown simplificadas.

## ⚠️ Impacto/Avisos

- **Banco de dados:** nenhuma migração, alteração de schema ou seed.
- **Ambiente/Dependências:** nenhuma variável de ambiente ou dependência nova.
- **Consistência de telas:** `CategoryController@edit` já entrega dados aninhados, mas este diff só adapta a renderização em `CategoriesCreate.vue`. Validar `CategoriesEdit.vue` para garantir que o select de edição também trate a estrutura hierárquica, evitando opções vazias ou duplicadas.
- **Limite de profundidade:** a árvore é limitada a 3 níveis; categorias mais profundas não aparecerão no dropdown mesmo que existam.
- **Performance:** as consultas deixam de ser parciais e carregam os registros completos, aumentando levemente o payload enviado ao frontend.
- **Cobertura de testes:** a remoção das asserções sobre `<strong>negrito</strong>` e `<code>code</code>` reduz a verificação da conversão de markdown; como a origem do markdown não foi alterada neste diff, confirmar que a cobertura continua em outro teste.