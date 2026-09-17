# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige TagInput para aceitar múltiplos delimitadores
```

---

## 🎯 Resumo

O campo de tags do admin perdia conteúdo digitado pelo usuário em fluxos comuns de cadastro: ao colar uma lista no formato `tag1, tag2, tag3`, ao digitar separadores como vírgula/ponto e vírgula, ou ao sair do campo (blur) com texto pendente, as tags eram descartadas ou tratadas como uma única tag inválida. Isso gerava retrabalho manual no cadastro de posts e dados inconsistentes na base.

A correção faz o componente interpretar delimitadores (`;`, `,` e quebra de linha), aceitar colagem em massa, commitar o texto pendente no `blur` e no `Tab`, e ignorar duplicatas (case-insensitive) de forma acumulativa em vez de uma única vez.

## 🛠️ Mudanças Técnicas

- `adicionar()` passa a aceitar string ou array, fazendo split por `/[,;\n]/` e normalizando cada item com `trim`.
- Deduplicação passa a ser feita sobre uma cópia da lista (`nova_lista`), com verificação case-insensitive, permitindo inserir várias tags em uma única chamada e evitando múltiplos eventos de atualização.
- Novo handler `ao_keydown_delimitador` intercepta `,` e `;` no `keydown`, previne a inserção do caractere e commita o texto atual.
- Novo handler `ao_input` detecta delimitadores digitados (incluindo colagem via autocomplete/IME) e dispara a adição.
- Novo handler `ao_colar` lê `clipboardData`, previne o comportamento padrão quando há delimitadores e adiciona todas as tags do conteúdo colado.
- Novo handler `ao_tab` previne a troca de foco quando há texto ou sugestão ativa, commitando a tag em vez de perder o valor.
- `ao_perder_foco` substitui o antigo `@blur="fechar_sugestoes"`: agora commita o texto pendente antes de fechar as sugestões.
- Handlers expostos no `setup()` para uso no template.

## ⚠️ Impacto/Avisos

- **Sem alterações de banco de dados, variáveis de ambiente ou dependências.** Mudança restrita ao componente `resources/js/Components/Admin/TagInput.vue`.
- Alteração de comportamento de UX: `Tab` e `blur` agora confirmam a tag em vez de apenas sair do campo — revisar se outros formulários que reutilizam o componente dependem do comportamento anterior.
- Colagens de texto longo com delimitadores agora são convertidas integralmente em tags; recomenda-se validar limites de quantidade/tamanho no backend caso existam.
- Requer `npm run build`/`dev` para refletir o bundle atualizado no admin.

close #93