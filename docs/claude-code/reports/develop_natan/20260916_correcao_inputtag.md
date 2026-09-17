# Relatório de Tarefa — Correção e Aprimoramento do TagInput

**Data:** 16 de setembro de 2026  
**Branch:** `develop_natan`  
**Tarefa:** Ajustar o componente `TagInput` para assegurar a persistência de tags na edição e criação de posts.

---

## 1. Resumo das Modificações

No arquivo [TagInput.vue](../../../../resources/js/Components/Admin/TagInput.vue):

1. **Commit Automático no Blur (`ao_perder_foco`):**
   - Ao perder o foco do input (por exemplo, ao clicar no botão "Salvar Alteração" ou "Publicar"), o texto restante no input é adicionado automaticamente ao array de tags.
2. **Suporte a Delimitadores de Vírgula e Ponto-e-Vírgula:**
   - Adicionados handlers `ao_keydown_delimitador` e `ao_input` para converter termos digitados imediatamente ao inserir vírgula (`,`) ou ponto-e-vírgula (`;`).
3. **Confirmação com a Tecla `Tab` (`ao_tab`):**
   - Pressionar `Tab` com texto digitado confirma a tag atual de forma intuitiva.
4. **Colagem de Tags em Lote (`ao_colar`):**
   - Suporte a colar listas de tags separadas por vírgula, ponto-e-vírgula ou quebras de linha.
5. **Normalização na Função `adicionar`:**
   - Divide entradas com múltiplos termos por `[,;\n]`, eliminando espaços extras e ignorando duplicatas (case-insensitive).

---

## 2. Documentos Relacionados

- Survey de fatos: [20260916_correcao_inputtag_surveyfacts.md](../../survey/20260916_correcao_inputtag_surveyfacts.md)
- Plano aprovado: [20260916_correcao_inputtag.md](../../plans/20260916_correcao_inputtag.md)

---

## 3. Validação

- Execução de `npm run build` concluída com código 0.
- Convenções de código (`snake_case`, ausência de `console.log`) respeitadas integralmente.

