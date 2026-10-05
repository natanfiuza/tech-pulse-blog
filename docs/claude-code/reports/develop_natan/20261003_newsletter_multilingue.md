# Relatório de Implementação: Newsletter Multilíngue — Skill, Envio por Idioma e Edição 01

- **Data:** 2026-10-03
- **Branch:** `develop_natan`
- **Tarefa:** (a) alterar a skill `generate-newsletter-body` para incluir a tradução do corpo nos idiomas suportados; (b) alterar `app/Console/Commands/NewsletterSendCommand.php` para enviar conforme o idioma de cada assinante; (c) adicionar à skill a pesquisa dos últimos artigos publicados, consultando a base de produção.

---

## 1. Contexto

A newsletter declara suportar 5 idiomas (`NewsletterTranslations::LANGS` = `pt_br`, `en`, `pt_pt`, `es`, `fr`) e o formulário de inscrição deixa o assinante escolher o idioma. Na prática, **nenhum e-mail saía traduzido**, por três motivos independentes:

1. **Não existia nenhum arquivo traduzido.** `../../../../public/content/newsletter/01/` tinha só `newsletter_body.md` (português).
2. **O chrome dos e-mails era português fixo** — `<html lang="pt-BR">` e a tagline "O Pulsar da Tecnologia" hardcoded nos três templates.
3. **A skill não mandava gerar traduções** e buscava os posts com uma ordenação que escondia os artigos mais novos.

Decisões confirmadas com o usuário: traduzir para **os 4 idiomas** (`en`, `pt_pt`, `es`, `fr`); atualizar a edição 01 para os **4 posts mais recentes** (68, 67, 66, 65); corrigir **os três** templates.

---

## 2. Achado principal: a consulta da skill escondia os posts mais novos

A skill instruía `Post::publicado()->latest('published_at')`. Existem **6 posts publicados com `published_at` nulo** (ids 68, 67, 66, 65, 63, 60) — e o **MySQL ordena NULL por último** em `ORDER BY ... DESC`. O `take(5)` portanto nunca chegava até eles.

Consulta executada na base de produção (`srv1062.hstgr.io` / `u147216022_tech_pulse`), somente leitura:

```
--- latest(created_at) take(10) ---
68   | 2026-10-03   | pub=NULL         | O PostgreSQL 19 Não Aposentou o pgvector
67   | 2026-09-30   | pub=NULL         | O PHP Tem Duas Datas de Morte, Não Uma
66   | 2026-09-29   | pub=NULL         | O Domínio de Exemplo que Ninguém Comprou Ainda
65   | 2026-09-28   | pub=NULL         | Autorregulação da IA: Quem É Auditado Escreve a Auditoria
64   | 2026-09-18   | pub=2026-09-17   | Cory Doctorow e a Degradação das Plataformas de IA
63   | 2026-09-18   | pub=NULL         | Kate Crawford e a Matéria Invisível da IA
...

--- o que a consulta ANTIGA (latest published_at) devolvia ---
64   | Cory Doctorow e a Degradação das Plataformas de IA
62   | Arvind Narayanan e o Óleo de Cobra Digital
61   | Timnit Gebru: quem tem permissão para criticar a IA?
58   | Céticos da IA: como ler uma crítica sem engolir o tom
59   | Ed Zitron: como auditar uma promessa de IA

posts publicados com published_at NULL: 6
```

O post **68 foi criado hoje** (03/10/2026) e era invisível para a consulta antiga. A Home usa `created_at DESC` (`../../../../app/Http/Controllers/HomeController.php:22`) — é a ordenação canônica do site e a que a newsletter passou a espelhar.

---

## 3. O que foi feito

### 3.1 Skill — `../../../../.claude/skills/generate-newsletter-body/SKILL.md`

**Passo 2 (coleta de posts)** — reescrito:

- Consulta trocada para `latest('created_at')->take(10)`, com `created_at` e `published_at` expostos no JSON para o operador ver a diferença.
- **Armadilha documentada em bloco próprio**: o `published_at` é nullable, o `scopePublicado` aceita `IS NULL`, o MySQL joga NULL para o fim, e foi exatamente isso que produziu o resultado errado da edição 01 — com os ids e datas reais como evidência.
- **Aviso de que o `.env` aponta para produção**, com a instrução de executar apenas `SELECT` via `tinker` e nunca `migrate`/`db:seed`/`db:wipe`.
- Passo novo de **verificação de HTTP 200** por `curl`, com slug inexistente como controle negativo, e a explicação de que `WebFetch` devolve HTML vazio porque o front é uma SPA Inertia.
- A contagem de posts passou de 2–3 para **2–4**.

**Passo 4 (novo) — Traduzir o corpo para todos os idiomas**:

- Tabela dos 5 arquivos com o papel de cada um.
- **Regra explícita de não criar `newsletter_body.pt_br.md`**, com a cadeia de resolução documentada (`newsletter_body.{lang}.md` → `newsletter_body.md` → `newsletter_body.pt_br.md` → `newsletter_body.en.md`) e o motivo: o arquivo base é o **segundo** da cadeia e serve de fallback para qualquer idioma sem tradução; movê-lo para a variante `.pt_br` deixaria um assinante de idioma não suportado **sem corpo nenhum**.
- Regras de tradução: traduzir a prosa, **não** traduzir os títulos dos posts (são títulos reais, com slug correspondente) nem identificadores de código; preservar a voz e a estrutura; manter 400–700 palavras.
- Comando de verificação de que cada idioma resolve para o arquivo certo (comprimento e H1 distintos por idioma).

**Assunto do e-mail** — a skill afirmava que o assunto sai do corpo ("Gancho principal da edição"). Isso é falso: ele vem fixo de `NewsletterTranslations::for($lang)['mail_newsletter_subject']` e **não existe mecanismo de preview text**. A skill agora diz isso e remove o passo inexecutável. *(O mecanismo em si não foi alterado — segue como pendência registrada no relatório anterior.)*

**Seção de gravação** — atualizada para os 5 arquivos, com **aviso destacado contra rodar `newsletter:send`** (dispara e-mail real e grava `last_sent.txt`), o comando de ensaio seguro via `body_html()`, e a nota de que as traduções são geradas por IA e precisam de revisão humana.

### 3.2 Conteúdo — `../../../../public/content/newsletter/01/`

A seção "📝 Do blog TechPulse" foi atualizada para os 4 posts mais recentes. A frase de abertura "O blog estreou a série **Céticos da IA**" **saiu** — os 4 posts novos não formam essa série (são PostgreSQL, ciclo de vida do PHP, domínios reservados e regulação de IA). Substituída por uma abertura neutra ("Confira o que saiu no blog nas últimas semanas:").

Quatro arquivos novos de tradução, gerados a partir do base pt-BR:

| Arquivo | Idioma |
|---|---|
| `../../../../public/content/newsletter/01/newsletter_body.en.md` | Inglês |
| `../../../../public/content/newsletter/01/newsletter_body.pt_pt.md` | Português (Portugal) |
| `../../../../public/content/newsletter/01/newsletter_body.es.md` | Espanhol |
| `../../../../public/content/newsletter/01/newsletter_body.fr.md` | Francês |

Em todos: prosa traduzida, **títulos dos posts preservados em português**, identificadores de código intactos, mesma estrutura de headings/emojis/links, e o `pt_pt` usando "partilhar", "bases de dados" e "ficheiros".

### 3.3 Traduções — `../../../../app/Support/NewsletterTranslations.php`

Quatro chaves novas nos 5 arrays de idioma:

| Chave | Usada em | Substitui |
|---|---|---|
| `mail_newsletter_tagline` | os 3 templates | `O Pulsar da Tecnologia` |
| `mail_newsletter_edition_badge` | `newsletter.blade.php` | `Edição #{{ $edition }}` (com `{edition}`) |
| `mail_newsletter_footer_reason` | `newsletter.blade.php` | `Você recebeu este e-mail porque…` |
| `mail_cancel_footer_note` | `cancel-link.blade.php` | `Se você não solicitou este cancelamento…` |

A tagline de cada idioma foi extraída da parte após o travessão do `mail_newsletter_subject` já existente, para manter coerência com o assunto. No `footer_reason`, o `<strong>TechPulse</strong>` foi removido do texto para não exigir `{!! !!}`.

### 3.4 Templates — `../../../../resources/views/emails/`

Nos três (`newsletter.blade.php`, `confirmation.blade.php`, `cancel-link.blade.php`):

- `<html lang="pt-BR">` → `<html lang="{{ str_replace('_', '-', $lang) }}">`, seguindo o padrão de `../../../../resources/views/app.blade.php:2`.
- Strings fixas trocadas por `$strings[...]` com fallback `?? 'texto padrão'`, no mesmo estilo já usado nos arquivos.

### 3.5 Mailables — `../../../../app/Mail/`

`NewsletterMail`, `ConfirmationMail` e `CancelLinkMail` agora passam `'lang' => $this->lang` no array `with` de `content()`. Sem isso o `<html lang>` dinâmico não teria de onde ler — `$this->locale($lang)` do Mailable só afeta a camada nativa do Laravel, que este projeto não usa.

### 3.6 Command — `../../../../app/Console/Commands/NewsletterSendCommand.php`

O loop **já era lang-aware** e não mudou. Três ajustes:

- **Fail-fast corrigido.** Antes validava `body_markdown($edition, 'pt_br')` num `try/catch`; uma edição que só tivesse `newsletter_body.es.md` seria rejeitada antes do loop. Agora usa o novo `NewsletterContent::edition_exists($edition)`, que checa se a pasta tem **algum** arquivo de corpo.
- **Aviso de idioma sem tradução.** Antes do loop, para cada idioma que tenha assinante ativo mas não tenha `newsletter_body.{lang}.md`, emite `warn` dizendo que esses assinantes receberão o corpo em pt_br.
- **`lang` normalizado uma vez** por assinante, reusado no `body_html()`, no `strings` e na `unsubscribeUrl`. Antes o `$subscriber->lang` cru ia para a URL de descadastro, permitindo `?lang=` inválido.

### 3.7 Support — `../../../../app/Support/NewsletterContent.php`

Dois métodos novos, sem alterar a cadeia de fallback existente:

- `edition_exists(string $edition): bool` — a pasta existe e tem algum `newsletter_body*.md`.
- `body_exists(string $edition, string $lang = 'pt_br'): bool` — existe o arquivo dedicado ao idioma, **sem** considerar fallback (é o que distingue "traduzido" de "caiu no base").

### 3.8 Testes — `../../../../tests/Feature/NewsletterSendCommandTest.php`

Dois testes novos (de 4 para 6):

- `test_envia_cada_assinante_no_idioma_dele` — cria `newsletter_body.en.md` na fixture e assere que o assinante `pt_br` recebe o corpo em português (e não o inglês), o `en` recebe o inglês (e não o português), cada um com o `lang` e o `?lang=` de descadastro corretos.
- `test_aceita_edicao_que_so_tem_corpo_traduzido` — edição com apenas `newsletter_body.es.md` e um assinante `es` é aceita e enviada (cobre o `edition_exists`).

---

## 4. Testes e Validações

### 4.1 Suíte de newsletter (arquivo por arquivo, conforme `CLAUDE.md`)

```
=== NewsletterSubscribeTest ===      OK (7 tests, 44 assertions)
=== NewsletterConfirmTest ===        OK (6 tests, 49 assertions)
=== NewsletterCancelTest ===         OK (4 tests, 24 assertions)
=== NewsletterSendCommandTest ===    OK (6 tests, 17 assertions)
```

**23 testes, 134 asserções, todos passando.** O teste pré-existente que garante o fallback (fixture só com `newsletter_body.md`, assinante `en` recebendo e-mail) continua verde — a cadeia de fallback não foi tocada.

### 4.2 Formatação

```
vendor/bin/pint --test  →  PASS  7 files
```

### 4.3 Resolução por idioma na edição 01

```bash
php artisan tinker --execute="foreach (App\Support\NewsletterTranslations::LANGS as \$l) { ... }"
```

| Idioma | Bytes | Palavras (prosa) | H1 |
|---|---|---|---|
| `pt_br` | 4804 | 670 | `# TechPulse Newsletter — Edição #01` |
| `en` | 4538 | 605 | `# TechPulse Newsletter — Edition #01` |
| `pt_pt` | 4790 | 673 | `# TechPulse Newsletter — Edição #01` |
| `es` | 4892 | 690 | `# TechPulse Newsletter — Edición #01` |
| `fr` | 5113 | 692 | `# TechPulse Newsletter — Édition #01` |

Comprimentos e H1s distintos em todos — nenhum idioma caiu no fallback silenciosamente. A contagem de prosa exclui URLs, blocos de código e sintaxe markdown; a contagem bruta (`str_word_count` no arquivo cru) fica entre 683 e 779, inflada pelas URLs dos links. **Todas as 5 dentro da faixa 400–700 palavras** exigida por `../../../specs/newsletter/TechPulse_template_newsletter_edicao_01.md:94`. O francês saiu inicialmente com 704 e foi aparado em ~12 palavras.

### 4.4 Render dos 3 templates nos 5 idiomas

15 renders executados via `view(...)->render()`, com asserção positiva (as strings do próprio idioma devem aparecer) e negativa (valores pt-BR que **diferem** não podem vazar). Resultado: **15/15 OK**, incluindo `<html lang>` correto e o badge `Edição/Edición/Édition #01` por idioma.

> O `fr/cancel-link` apontou falha na primeira rodada: a checagem comparava o valor cru, mas o francês tem apóstrofos e o `{{ }}` do Blade os escapa para `&#039;`. Verificado que o valor **está** no HTML, apenas escapado — comportamento correto, que exibe `'` no cliente de e-mail. Falso positivo do script de verificação.

### 4.5 Estado da base no momento do fechamento

```
ativos por idioma: {"pt_br":2}
total ativos: 2
ultima edicao enviada: NULL
latest_edition(): 01
edition_exists(01): true
```

### 4.6 Não executado

`php artisan newsletter:send 01` **não foi rodado**, deliberadamente: dispara e-mail real para os assinantes e grava `last_sent.txt`, marcando a edição como enviada. O agendador (`../../../../app/Console/Kernel.php:16`) já dispara sozinho na **segunda, 05/10/2026, às 08:00**, e `latest_edition()` devolve `01`.

---

## 5. Observações e pendências

1. **As traduções são geradas por IA e serão enviadas sob o nome do Nataniel.** Precisam de **revisão humana antes do disparo de segunda**, em especial o `pt_pt`, onde uma tradução ruim é mais perceptível para o leitor do que a ausência dela.
2. **Ninguém recebe as traduções hoje.** São 2 assinantes ativos, **ambos `pt_br`** — o trabalho é de infraestrutura para quando houver assinantes nos outros idiomas. O aviso do comando (`warn`) torna esse estado visível a cada envio futuro.
3. **O assunto do e-mail continua fixo** (`TechPulse #{edition} — O Pulsar da Tecnologia`) e não reflete o gancho editorial da edição. A skill agora documenta a limitação em vez de instruir um passo impossível; alterar o mecanismo permanece fora de escopo.
4. **O `NewsletterConfirmation` não persiste o `lang` escolhido no cadastro.** Se o link de confirmação for aberto sem `?lang=`, o assinante nasce `pt_br` mesmo tendo escolhido outro idioma no formulário. Não investigado a fundo nesta tarefa — candidato à próxima.
5. **Avisos de lint pré-existentes mantidos.** O markdownlint segue sinalizando `MD036` (ênfase no lugar de heading) nos 5 arquivos de corpo — é o padrão do próprio template. No PHP, variáveis `camelCase` pré-existentes (`$pathsToTry` e `$normalizedLang` em `NewsletterContent`, `$estimatedSeconds` no command) não foram renomeadas, para não misturar limpeza de estilo com a mudança funcional.
6. **A seção "Do blog TechPulse" agora tem 4 posts** (antes 3). É o limite superior do que a skill passou a permitir; se o volume crescer, vale reavaliar o teto de palavras.
