# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: envia e-mails da newsletter no idioma do assinante
```

---

## 🎯 Resumo

Os e-mails transacionais da newsletter estavam com todos os textos fixos em português, mesmo para assinantes cadastrados em outros idiomas. Este PR torna o envio realmente multilíngue: o comando de disparo passa a escolher o corpo da edição e as strings de interface com base no idioma normalizado de cada inscrito, e os templates Blade deixam de ter rótulos hardcoded. O objetivo é melhorar a experiência de assinantes internacionais e reduzir o risco de enviar conteúdo em idioma errado em uma base já segmentada por `lang`.

## 🛠️ Mudanças Técnicas

- `NewsletterSendCommand`: normaliza o idioma do assinante via `NewsletterTranslations::normalize()` antes do render e usa esse valor em `body_html`, `strings`, `lang` do Mailable e no `unsubscribeUrl`.
- `NewsletterSendCommand`: a validação fail-fast deixa de exigir o corpo em `pt_br` e passa a usar `NewsletterContent::edition_exists()`, aceitando edições que só tenham corpo traduzido.
- `NewsletterSendCommand`: emite `warn` listando os idiomas com assinantes que não possuem tradução própria e que, portanto, cairão no fallback pt-BR.
- `NewsletterContent`: novos métodos `edition_exists()` (existe diretório e qualquer `newsletter_body*.md`) e `body_exists()` (existe arquivo dedicado ao idioma, sem considerar fallback).
- `NewsletterTranslations`: novas chaves `mail_newsletter_tagline`, `mail_newsletter_edition_badge`, `mail_newsletter_footer_reason` e `mail_cancel_footer_note` nos cinco idiomas suportados (pt-BR, en, pt-PT, es, fr).
- `NewsletterMail`, `ConfirmationMail` e `CancelLinkMail`: repassam `lang` para as views.
- Templates de e-mail (`newsletter`, `confirmation`, `cancel-link`): atributo `<html lang>` dinâmico, uso de `$strings[...]` com fallback em pt-BR e interpolação de `{edition}` no badge de edição.
- Testes: cobertura para envio no idioma de cada assinante (incluindo `lang` na URL de descadastro) e para edição que possui apenas corpo traduzido.

## ⚠️ Impacto/Avisos

- **Banco de dados:** sem migrations ou alterações de schema.
- **Variáveis de ambiente:** nenhuma nova variável ou alteração de configuração.
- **Dependências:** nenhuma dependência adicionada ou removida.
- **Comportamento:** edições sem `newsletter_body.md` base agora são aceitas, desde que exista ao menos um arquivo de corpo; assinantes sem tradução dedicada continuam recebendo o conteúdo em pt-BR, com aviso no output do comando.
- **Conteúdo:** novos textos de e-mail dependem das chaves adicionadas em `NewsletterTranslations`; traduções futuras devem manter a nomenclatura `mail_*`.
- **Referência de commit:** `.gitpr.commit.md`.

close #146

---

[![GitPR](https://img.shields.io/badge/GitPR-0_errors_%C2%B7_4_warnings-yellow)](https://gitpr.natanfiuza.dev.br/)