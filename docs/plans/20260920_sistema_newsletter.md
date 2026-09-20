# Plano de Implementação: Sistema de Newsletter Semanal TechPulse

Implementação do sistema completo de Newsletter para o **TechPulse Blog**, com periodicidade semanal (toda segunda-feira pela manhã), fluxo de segurança com **Double Opt-in**, cancelamento em 1 clique (RFC 8058), páginas no frontend (Inertia/Vue 3), templates de e-mail Blade com a identidade visual do blog, comando Artisan de envio (`newsletter:send`), e uma **Skill** para geração assistida de edições em Markdown baseadas nos artigos cadastrados no banco de dados e no template oficial.

---

## 1. Visão Geral e Arquitetura

O sistema segue a especificação do `gitpr_site`, adaptando a fonte de conteúdo de arquivos Markdown locais para os **Posts cadastrados no banco de dados do TechPulse**.

```mermaid
sequenceDiagram
    autonumber
    actor Leitor as Leitor / Visitante
    participant Box as Home / NewsletterBox.vue
    participant Ctrl as NewsletterController
    participant DB_Conf as newsletter_confirmations
    participant Mail as SMTP Mailer
    participant PageConf as NewsletterConfirmPage.vue
    participant DB_Sub as newsletter_subscribers
    participant Cmd as Artisan newsletter:send

    Leitor->>Box: Digita e-mail e clica em Inscrever
    Box->>Ctrl: POST /newsletter/subscribe { email }
    Ctrl->>DB_Conf: Gera token UUID com status pendente (24h)
    Ctrl->>Mail: Dispara ConfirmationMail
    Ctrl-->>Box: Flash status 'sent'

    Leitor->>PageConf: Abre link do e-mail: GET /newsletter/confirm/{uuid}
    PageConf-->>Leitor: Exibe formulário de confirmação de perfil
    Leitor->>PageConf: Preenche Nome, GitHub, Telefone e Idioma
    PageConf->>Ctrl: POST /newsletter/confirm/{uuid}
    Ctrl->>DB_Sub: Salva assinante ativo (is_canceled = false)
    Ctrl->>DB_Conf: Marca confirmação como realizada
    Ctrl-->>PageConf: Redireciona com status 'already_confirmed'

    Note over Cmd,DB_Sub: Toda segunda-feira às 08:00
    Cmd->>DB_Sub: Busca assinantes ativos
    Cmd->>Mail: Dispara NewsletterMail com corpo Markdown convertido em HTML
```

---

## 2. Mudanças Propostas

### Banco de Dados & Models

#### [NEW] [Migration: newsletter_confirmations](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/database/migrations/2026_09_20_000001_create_newsletter_confirmations_table.php)
- Tabela `newsletter_confirmations`: `id`, `uuid`, `email`, `is_confirmed` (boolean default 0), `date_confirmed` (timestamp nullable), `created_at`, `updated_at`.
- Índices em `uuid` (único) e `email`.

#### [NEW] [Migration: newsletter_subscribers](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/database/migrations/2026_09_20_000002_create_newsletter_subscribers_table.php)
- Tabela `newsletter_subscribers`: `id`, `uuid`, `name`, `email` (único), `github` (nullable), `phone` (nullable), `lang` (default 'pt_br'), `is_canceled` (boolean default 0), `date_canceled` (timestamp nullable), `created_at`, `updated_at`.
- Índice em `is_canceled`.

#### [NEW] [Model: NewsletterConfirmation](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Models/NewsletterConfirmation.php)
- Casts para `is_confirmed` (bool) e `date_confirmed` (datetime).
- Scope `scopeNotExpired()` para filtrar links com menos de 24 horas.

#### [NEW] [Model: NewsletterSubscriber](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Models/NewsletterSubscriber.php)
- Casts para `is_canceled` (bool) e `date_canceled` (datetime).
- Scope `scopeActive()` para filtrar assinantes ativos (`is_canceled = false`).

---

### Backend & Suporte

#### [NEW] [Support: NewsletterTranslations](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Support/NewsletterTranslations.php)
- Centraliza traduções para os e-mails e interfaces em `pt_br` (padrão principal), `en`, `pt_pt`, `es`, `fr`.
- Suporte a substituição dinâmica de placeholders (ex: `{edition}`).

#### [NEW] [Support: NewsletterContent](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Support/NewsletterContent.php)
- Carrega o Markdown da edição localizada em `public/content/newsletter/{edition}/newsletter_body.md` (com fallback de idioma).
- Converte Markdown para HTML (GFM) via `Str::markdown()`.
- Gerencia o histórico de envio gravando em `storage/app/private/newsletter/last_sent.txt` (ou `storage/app/newsletter/last_sent.txt`).
- Método auxiliar para detectar automaticamente a edição mais recente gerada.

#### [NEW] [Mailables](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Mail/)
- `App\Mail\ConfirmationMail`: envio do e-mail com link de ativação Double Opt-in.
- `App\Mail\CancelLinkMail`: envio do link de cancelamento sob demanda.
- `App\Mail\NewsletterMail`: envio do corpo da edição com cabeçalhos RFC 8058 de descadastro em 1 clique (`List-Unsubscribe` e `List-Unsubscribe-Post`).

#### [NEW] [Templates Blade de E-mail](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/views/emails/)
- `resources/views/emails/confirmation.blade.php`: design elegante TechPulse (Midnight Blue `#000b2b`, destaques em azul `#2563eb`).
- `resources/views/emails/cancel-link.blade.php`: e-mail com link de cancelamento.
- `resources/views/emails/newsletter.blade.php`: container responsivo que renderiza o `{!! $htmlBody !!}` com rodapé de descadastro.

#### [NEW] [Controller: NewsletterController](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Http/Controllers/NewsletterController.php)
- `subscribe(Request $request)`: valida e-mail, reutiliza UUID se pendente e < 24h, dispara `ConfirmationMail`.
- `send_cancel_link(Request $request)`: resposta genérica anti-enumeração disparando link de cancelamento se existir.
- `confirm(Request $request, string $uuid)`: renderiza `NewsletterConfirmPage` (estados: `form`, `already_confirmed`, `expired`, `not_found`).
- `confirm_submit(Request $request, string $uuid)`: valida dados, cria/reativa `NewsletterSubscriber` e marca confirmação.
- `cancel(Request $request, string $uuid)`: renderiza `NewsletterCancelPage` (estados: `form`, `done`, `already_canceled`, `not_found`).
- `unsubscribe(Request $request, string $uuid)`: marca `is_canceled = true` e `date_canceled = now()`.

#### [MODIFY] [Rotas: routes/web.php](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/routes/web.php)
- Registrar rotas de `/newsletter/*` antes das rotas dinâmicas com parâmetros coringa.

---

### Frontend (Inertia & Vue 3)

#### [NEW] [Component: NewsletterBox.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Components/NewsletterBox.vue)
- Componente reativo que consome as props flash e exibe os estados: formulário, envio de confirmação (`sent`), e-mail já confirmado (`already_confirmed`), link de cancelamento enviado (`cancel_link_sent`).
- Utiliza convenção `snake_case` e Tailwind CSS v3.4.

#### [MODIFY] [Página: Home.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Pages/Home.vue)
- Substituir o formulário fictício no painel da sidebar pelo componente `NewsletterBox.vue` integrado com o backend.

#### [NEW] [Página: NewsletterConfirmPage.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Pages/NewsletterConfirmPage.vue)
- Tela completa de confirmação do Double Opt-in no estilo visual do TechPulse.

#### [NEW] [Página: NewsletterCancelPage.vue](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/resources/js/Pages/NewsletterCancelPage.vue)
- Tela completa de cancelamento e feedback de descadastro.

---

### Comando Artisan & Agendamento

#### [NEW] [Command: NewsletterSendCommand](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Console/Commands/NewsletterSendCommand.php)
- Assinatura: `php artisan newsletter:send {edition?} {--force} {--interval=5}`
- Mecanismos de segurança: anti-reenvio via `last_sent.txt`, fail-fast se Markdown não existir, estimativa de tempo e isolamento de erros por assinante com barra de progresso.

#### [MODIFY] [Console Kernel: app/Console/Kernel.php](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/app/Console/Kernel.php)
- Agendar o comando para rodar toda segunda-feira pela manhã:
  `$schedule->command('newsletter:send')->mondays()->at('08:00');`

---

### Skill do Agente: Geração da Newsletter

#### [NEW] [Skill: generate-newsletter-body](file:///c:/Users/nataniel/projetos/pessoal/tech-pulse/tech-pulse-blog/.claude/skills/generate-newsletter-body/SKILL.md)
- Instruções detalhadas para o agente consultar os artigos (`Post`) publicados no banco de dados.
- Estrutura baseada em `docs/specs/newsletter/estrutura_conteudo.md` e `docs/specs/newsletter/TechPulse_template_newsletter_edicao_01.md`.
- Geração das seções obrigatórias:
  1. 👋 Abertura pessoal
  2. 💡 Dica técnica da semana (snippet/atalho/boa prática)
  3. 📝 Do blog TechPulse (resumos e links dos posts recentes)
  4. 🔍 Curadoria da semana
  5. 🛠️ Por trás do código (bastidores, decisões de arquitetura)
  6. 💬 Sua vez (pergunta engajadora)
  7. 🔁 Antes de fechar (CTA e assinatura)
- **Suporte a seções opcionais/quinzenais/mensais quando solicitado:**
  - 🧰 Spotlight de ferramenta ou lib
  - 📊 Enquete / pergunta técnica aprofundada
  - 🏗️ Bastidores de projeto maior (arquitetura/refatoração real)
- Salva os arquivos gerados em `public/content/newsletter/{edition}/newsletter_body.md` (e variantes traduzidas se solicitadas).

---

## 3. Plano de Verificação

### Testes Automatizados (PHPUnit)
Criaremos suíte completa de testes cobrindo 100% dos fluxos em formato compatível com PHPUnit 10:
- `tests/Feature/NewsletterSubscribeTest.php`: inscrição inicial, Double Opt-in, reenvio, anti-enumeração de cancelamento.
- `tests/Feature/NewsletterConfirmTest.php`: validação de token, expiração (24h), criação de assinante ativo, bloqueio de adulteração de e-mail.
- `tests/Feature/NewsletterCancelTest.php`: descadastro, atualização de status `is_canceled`, link direto RFC 8058.
- `tests/Feature/NewsletterSendCommandTest.php`: leitura de markdown, envio para assinantes ativos, controle com `--force`, tratamento de falhas.

Execução:
```bash
php artisan test tests/Feature/NewsletterSubscribeTest.php
php artisan test tests/Feature/NewsletterConfirmTest.php
php artisan test tests/Feature/NewsletterCancelTest.php
php artisan test tests/Feature/NewsletterSendCommandTest.php
```

### Relatório de Finalização
Conforme a regra do repositório em `CLAUDE.md`, gerar o relatório em:
`docs/claude-code/reports/develop_natan/20260920_implementacao_newsletter.md`
