# 🚀 Sugestão de Pull Request

**Mensagem de Commit Recomendada:**
```text
feat: implementa newsletter com double opt-in e envio agendado
```

---

## 🎯 Resumo

Implementa o sistema completo de newsletter do TechPulse, substituindo o formulário fictício da Home por um fluxo real de **Double Opt-in** com confirmação por e-mail, coleta de perfil do assinante, cancelamento em conformidade com boas práticas anti-abuso (RFC 8058) e disparo semanal automatizado. A motivação é transformar o bloco decorativo da sidebar em um canal de comunicação auditável, com consentimento explícito, gestão de idioma por assinante, rastreamento de cancelamentos e proteção contra envios acidentais ou duplicados.

## 🛠️ Mudanças Técnicas

- **Banco de dados**: cria as migrations `newsletter_confirmations` (tokens de confirmação de 24h) e `newsletter_subscribers` (assinantes, com `uuid`, `lang`, `is_canceled`, `date_canceled` e índices).
- **Models**: adiciona `NewsletterConfirmation` (scope `notExpired`) e `NewsletterSubscriber` (scope `active`).
- **Controller `NewsletterController`**: fluxos de `subscribe` (reaproveita confirmação pendente < 24h), `send_cancel_link` (com resposta genérica anti-enumeração), `confirm`, `confirm_submit` (ativa/reativa assinante validando e-mail contra o token), `cancel` e `unsubscribe`.
- **Mailables**: `ConfirmationMail`, `CancelLinkMail` e `NewsletterMail` (este último injeta headers `List-Unsubscribe` e `List-Unsubscribe-Post=One-Click`).
- **Views de e-mail** (Blade inline-styled) para confirmação, cancelamento e corpo da newsletter em HTML.
- **Suporte**: `NewsletterContent` (detecta última edição, faz fallback de idioma, converte Markdown em HTML e persiste a última edição enviada em `storage/app/local/newsletter/last_sent.txt`) e `NewsletterTranslations` (i18n para `pt_br`, `en`, `pt_pt`, `es`, `fr`).
- **Comando Artisan `newsletter:send`**: dispara a edição para todos os assinantes ativos com progress bar, `--force` (reenvio e bypass de tempo estimado) e `--interval` (throttle entre envios); fail-fast se o Markdown da edição não existir e resiliente a falhas individuais.
- **Scheduler** (`app/Console/Kernel.php`): agenda `newsletter:send` todas as segundas-feiras às 08:00.
- **Frontend Vue 3 + Inertia**: novos componentes `NewsletterBox` (estados de envio, já inscrito e link de cancelamento), páginas `NewsletterConfirmPage` (formulário de perfil com nome, GitHub, telefone e idioma) e `NewsletterCancelPage`; `Home.vue` passa a consumir `NewsletterBox`.
- **Rotas**: adiciona o grupo `newsletter.*` em `routes/web.php`.
- **Testes**: cobre inscrição, confirmação (incluindo expiração, e-mail divergente e reativação), cancelamento (anti-enumeração e idempotência) e o comando de envio (filtro de ativos, bloqueio de reenvio e `--force`).
- **Chore**: normalização de estilo (PSR) em arquivos de `scratch/`.

## ⚠️ Impacto/Avisos

- **Banco de dados**: é necessário rodar `php artisan migrate` — duas novas tabelas são criadas.
- **Scheduler**: o disparo automático depende de cron configurado (`php artisan schedule:run`). Sem isso, o comando precisará ser executado manualmente.
- **Storage**: o controle de "última edição enviada" usa o disco `local` em `newsletter/last_sent.txt`; não é persistido em banco, o que exige atenção em ambientes com storage efêmero (containers/redeploys apagariam o marcador e poderiam gerar reenvio).
- **Conteúdo**: as edições devem existir em `public/content/newsletter/{edition}/newsletter_body.{lang}.md` — o comando falha (fail-fast) quando o Markdown não está disponível.
- **E-mail transacional**: os fluxos dependem de SMTP/`MAIL_*` já configurados; o envio não usa filas, portanto lotes grandes podem estourar o tempo de execução (mitigado por aviso quando o tempo estimado ultrapassa 1h).
- **Anti-enumeração**: `POST /newsletter/send-cancel-link` sempre retorna o mesmo status, mesmo para e-mails inexistentes — comportamento intencional, coberto por teste.
- **Sem novas variáveis de ambiente ou dependências** adicionadas.


close #108
---

[![GitPR](https://img.shields.io/badge/GitPR-0_errors_%C2%B7_51_warnings-yellow)](https://gitpr.natanfiuza.dev.br/)