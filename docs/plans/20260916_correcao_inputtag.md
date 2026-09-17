# Plano de Implementação — Correção e Aprimoramento do TagInput

Este plano documenta o ajuste no componente [`TagInput.vue`](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Components/Admin/TagInput.vue) para assegurar que as tags sejam salvas corretamente tanto na criação quanto na alteração de posts.

---

## 1. Motivação e Diagnóstico

Ao criar ou editar um post, se o usuário digitava o nome de uma tag e clicava diretamente no botão de salvar sem antes pressionar `Enter`, o texto digitado não era incluído na lista de tags (`form.hashtags`). Além disso, delimitadores como vírgula (`,`), ponto-e-vírgula (`;`) ou tecla `Tab` não eram interceptados.

---

## 2. Modificações Realizadas

### Componente Frontend ([TagInput.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Components/Admin/TagInput.vue))

1. **Commit no `@blur` (`ao_perder_foco`):**
   - Ao perder o foco do campo de entrada, qualquer texto pendente no input é automaticamente adicionado à lista de tags antes de fechar as sugestões.
2. **Suporte a Delimitadores (`ao_keydown_delimitador` e `ao_input`):**
   - Digitar vírgula (`,`) ou ponto-e-vírgula (`;`) adiciona o termo digitado como tag imediatamente e limpa o campo.
3. **Navegação com `Tab` (`ao_tab`):**
   - Pressionar a tecla `Tab` com texto digitado confirma a tag atual sem perder a fluidez.
4. **Colagem em Lote (`ao_colar`):**
   - Colar textos contendo vírgulas, ponto-e-vírgula ou quebras de linha divide o conteúdo em múltiplas tags individuais.
5. **Divisão de Múltiplos Termos (`adicionar`):**
   - A função `adicionar` aceita strings delimitadas por `[,;\n]` e adiciona termos únicos (case-insensitive).

---

## 3. Validação

- Compilação via `npm run build` executada com sucesso.
- Convenções de código estritas em `snake_case` e sem logs.
