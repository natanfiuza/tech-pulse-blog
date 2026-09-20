# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
fix: corrige cache do avatar e adiciona tema claro no perfil
```

---

## 🎯 Resumo

Usuários relatavam que, após trocar ou remover a foto de perfil, o avatar antigo continuava aparecendo na interface e nas páginas públicas. A causa era o cache agressivo (`public, max-age=86400`) combinado com uma URL de avatar imutável, além de o `updated_at` do usuário não ser atualizado ao alterar apenas a imagem. Este PR invalida o cache de forma determinística (via versionamento por timestamp) e passa a resolver corretamente avatares oriundos de provedores externos (Google). Aproveita para introduzir suporte consistente ao modo claro na tela de perfil e no post.

## 🛠️ Mudanças Técnicas

- `User::getAvatarUrlAttribute()` passa a anexar `?v={updated_at}` à rota do avatar, versionando a URL a cada alteração de perfil.
- `UserProfileController::update()` chama `$user->touch()` antes do `save()` nos fluxos de remoção e definição de avatar, garantindo mudança do `updated_at`.
- `UserProfileImageController::show()` agora distingue avatar externo: se `avatar` for uma URL `http`, importa via `AvatarService::salvar_avatar_google()`; caso contrário, gera o avatar padrão.
- `Cache-Control` da resposta de imagem alterado de `public, max-age=86400` para `no-cache, private, must-revalidate`.
- `ProfileMasterDetails.vue`: nova ref reativa `avatar_url_exibicao` com `watch` sobre `props.profile_user.avatar_url`, cache-busting por `Date.now()` no reset e no sucesso do upload, e fallback via `@error` na tag `img`.
- Remoção do campo `avatar_url` do `useForm` (fonte de verdade passa a ser a prop/ref).
- Ajustes de tema claro/escuro (`slate`/`dark:`) e pequenos refinamentos visuais em `ProfileMasterDetails.vue` e `Post.vue`.
- Limpeza de imports não utilizados (`Illuminate\Support\Facades\File`, `App\Models\UserProfile`).

## ⚠️ Impacto/Avisos

- **Cache/Performance:** os avatares deixam de ser cacheáveis por 24h no navegador/CDN, aumentando as requisições ao backend e leituras em disco. Se houver CDN, revisar regras de cache para a rota `user.avatar`.
- **Banco de dados:** nenhuma migration nova; as migrations tocadas tiveram apenas linhas em branco removidas (sem mudança de schema).
- **Env/Dependências:** nenhuma variável de ambiente ou dependência nova.
- **Integração externa:** avatares com URL `http` agora disparam download/importação via `salvar_avatar_google()`, dependendo de conectividade de saída.
- **Arquivos indevidos no commit:** `scratch/test_*.php` e `test_img` são scripts/artefatos de depuração e não deveriam ir para o repositório. Recomenda-se removê-los e adicionar `scratch/` ao `.gitignore`.
- **Testes:** `tests/Feature/UserProfileTest.php` teve apenas o import de `UserProfile` removido; não há cobertura nova para a invalidação de cache do avatar.

> Mensagem de commit correspondente disponível em `.gitpr.commit.md`.


close #104

---

[![GitPR](https://img.shields.io/badge/GitPR-no_issues-brightgreen)](https://gitpr.natanfiuza.dev.br/)