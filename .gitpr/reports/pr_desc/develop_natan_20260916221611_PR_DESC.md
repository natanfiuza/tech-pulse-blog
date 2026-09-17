# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: inclui tag pendente ao salvar post e normaliza hashtags
```

---

## 🎯 Resumo

Ao criar ou editar um post, a última tag digitada no campo de tags era silenciosamente descartada quando o usuário clicava em **Salvar/Publicar** sem antes pressionar `Enter` ou clicar em "Adicionar". O post era persistido sem essa tag, exigindo edição manual posterior. Esta mudança garante que o texto pendente no input seja convertido em tag no momento do envio do formulário e endurece a comparação de hashtags usada para detectar alterações, evitando falsos negativos de "nenhuma alteração" na tela de edição.

## 🛠️ Mudanças Técnicas

- `TagInput.vue`: expõe o método `confirmar_texto()` (além de `adicionar` e `remover`) via `expose`, permitindo que o componente pai force a conversão do texto pendente em tag.
- `TagInput.vue`: introduz o computed `tags_ativas`, que resolve a lista de tags considerando `model_value` e `modelValue`, tornando o componente compatível com os dois nomes de prop/v-model (`update:model_value` e `update:modelValue`).
- `TagInput.vue`: adiciona a prop `texto_pendente` com sincronização bidirecional (watchers + `update:texto_pendente`), permitindo que o formulário pai saiba que existe texto não confirmado.
- `TagInput.vue`: normaliza todas as tags com `String(...)` em filtros, deduplicação e remoção, evitando erros quando a lista contém valores não-string (ex.: objetos de hashtag vindos da API).
- `PostsCreate.vue`: guarda a referência do componente (`tag_input_ref`) e chama `confirmar_texto()` no início de `submit()`, antes da codificação do conteúdo.
- `PostsEdit.vue`: mesmo ajuste de `confirmar_texto()` no `submit()`, mais binding `v-model:texto_pendente="texto_pendente_tag"`.
- `PostsEdit.vue`: `houve_alteracao` passa a retornar `true` quando há texto de tag pendente, bloqueando salvamentos "vazios" que perderiam a tag digitada.
- `PostsEdit.vue`: comparação de arrays (`arrays_sao_iguais`) agora faz `trim()` + `toLowerCase()` + `String()`, tornando a detecção de mudanças insensível a caixa e espaços.
- `PostsEdit.vue`: normalização de `hashtags` iniciais aceita tanto objetos (`{ name }`) quanto strings, cobrindo variações de payload.
- Documentação de apoio (PR desc, survey, walkthrough e relatório de edição de post) atualizada junto à mudança.

## ⚠️ Impacto/Avisos

- **Banco de dados:** nenhuma migration, alteração de schema ou backfill. Sem impacto em dados existentes.
- **Variáveis de ambiente:** nenhuma nova variável ou alteração de configuração.
- **Dependências:** nenhuma dependência adicionada, removida ou atualizada.
- **API pública:** sem alteração de contrato; o impacto é restrito ao painel administrativo (Vue/Inertia).
- **Comportamento do usuário:** clicar em Salvar/Publicar agora confirma automaticamente a tag digitada. Na tela de edição, um texto de tag não confirmado passa a marcar o formulário como alterado, o que pode habilitar o botão de salvar com mais frequência (comportamento desejado).
- **Compatibilidade:** a dupla aceitação de `model_value`/`modelValue` é uma camada de compatibilidade; recomenda-se padronizar em `modelValue` em iterações futuras para remover o código de fallback.