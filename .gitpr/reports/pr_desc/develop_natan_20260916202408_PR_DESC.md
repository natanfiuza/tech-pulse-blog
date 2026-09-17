# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige botões de edição de post publicado
```

---

## 🎯 Resumo
Evitar salvamentos desnecessários e tornar a interface mais clara ao editar posts já publicados. O botão principal agora indica **"Salvar Alteração"** para posts publicados e permanece desabilitado enquanto não houver mudanças reais em relação ao estado original. O botão secundário (rascunho) é ocultado nesses casos, impedindo ações incoerentes com o status do post.

## 🛠️ Mudanças Técnicas
- Adiciona computed `eh_publicado` baseado em `post.status`.
- Implementa computed `houve_alteracao` comparando campos do formulário (`title`, `content`, `excerpt`, `category_id`, `published_at`, `hashtags`) com os valores originais do post.
- Adiciona utilitário `arrays_sao_iguais` para comparar hashtags independentemente da ordem.
- Armazena snapshots iniciais: `hashtags_iniciais` e `published_at_inicial`.
- Altera o rótulo do botão principal para `eh_publicado ? 'Salvar Alteração' : 'Publicar'`.
- Desabilita o botão principal quando `form.processing` ou quando o post está publicado e não houve alteração.
- Oculta o botão secundário (`v-if="!eh_publicado"`) para posts publicados.
- Adiciona early return em `submit()` para bloquear envio sem alterações em post publicado.

## ⚠️ Impacto/Avisos
- Nenhuma alteração em banco de dados, variáveis de ambiente ou dependências.
- Mudança restrita ao frontend (Vue 3 + Inertia.js).
- Posts publicados só podem ser salvos após detecção de modificação; o fluxo de publicação inicial permanece inalterado.
- A comparação de conteúdo usa `original_content.value`, garantindo detecção de mudanças no corpo do post editado no editor rich text.