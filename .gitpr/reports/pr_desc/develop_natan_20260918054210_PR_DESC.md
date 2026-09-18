# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: adiciona alternador de tema claro e escuro
```

---

## 🎯 Resumo

O blog operava exclusivamente em modo escuro, sem oferecer escolha ao visitante e sem respeitar a preferência do sistema operacional. Esta mudança introduz um alternador de tema persistente entre claro e escuro, disponível tanto na navbar pública quanto na topbar administrativa, garantindo consistência visual em todas as superfícies da aplicação (conteúdo de artigos, tabelas, formulários, cabeçalho e scrollbar).

A preferência do usuário passa a ser respeitada e lembrada entre sessões, enquanto novos visitantes recebem o tema conforme `prefers-color-scheme`, evitando o flash de tema incorreto no primeiro carregamento.

## 🛠️ Mudanças Técnicas

- Cria o composable `resources/js/Composables/use_theme.js` com estado reativo compartilhado (`tema_atual`) e as funções `alternar_tema`, `definir_tema` e `inicializar_tema`.
- Persiste a escolha do usuário em `localStorage` sob a chave `techpulse_tema`, com tratamento silencioso para ambientes de armazenamento restrito.
- Adiciona script inline em `resources/views/app.blade.php` que aplica a classe `dark` no `<html>` antes da renderização, eliminando o FOUC (flash of unstyled content).
- Insere botão de alternância na `Navbar.vue` (público) e na `Topbar.vue` (admin), com ícone dinâmico (`light_mode`/`dark_mode`) e rótulos acessíveis via `aria-label` e `title`.
- Amplia `resources/css/tailwind.css` com regras `html:not(.dark)` que sobrescrevem cores de fundo, texto, bordas, inputs, scrollbar e blocos de artigo para o tema claro.
- Redesenha o bloco `pre.mermaid` com fundo, borda, padding e borda arredondada, corrigindo a legibilidade dos diagramas que antes ficavam transparentes.
- Inclui documentação de apoio sobre implementação de dark mode com Vue/Inertia/Tailwind e relatórios de execução.

## ⚠️ Impacto/Avisos

- **Banco de dados:** nenhuma migration ou alteração de schema.
- **Variáveis de ambiente:** nenhuma nova variável; não requer ajuste em `.env` ou CI.
- **Dependências:** nenhuma dependência nova adicionada ou atualizada.
- **Preferência do usuário:** a chave `techpulse_tema` é gravada no `localStorage` do navegador; usuários que bloqueiam armazenamento continuam funcionando, mas sem persistência entre sessões.
- **Compatibilidade:** o tema padrão permanece escuro quando não há preferência detectável, preservando a identidade visual atual do blog.
- **Manutenção de CSS:** boa parte do tema claro usa `!important` sobre classes utilitárias do Tailwind; futuras classes de cor podem exigir regras correspondentes nesse bloco.


close #98