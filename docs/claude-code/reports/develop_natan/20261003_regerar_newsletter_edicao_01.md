# Relatório de Implementação: Regeneração do Corpo da Newsletter — Edição 01

- **Data:** 2026-10-03
- **Branch:** `develop_natan`
- **Tarefa:** Regenerar `public/content/newsletter/01/newsletter_body.md`, substituindo os posts desatualizados pela série **Céticos da IA** e removendo a seção opcional de Spotlight, conforme decisões editoriais do usuário.

---

## 1. Contexto

A edição 01 já existia em `../../../../public/content/newsletter/01/newsletter_body.md`, mas a seção "Do blog TechPulse" apontava para posts de **dezembro de 2024**. O blog publicou desde então a série **Céticos da IA** (16–17/09/2026), que é o conteúdo recente que a edição deveria destacar.

Dois fatos apurados antes da execução:

- **A edição 01 nunca foi enviada.** `storage/app/` não possui `newsletter/last_sent.txt`, e `NewsletterContent::last_sent_edition()` retorna `null` nesse caso (`../../../../app/Support/NewsletterContent.php:77`). Não houve risco de reenvio.
- **O agendador dispara sozinho.** `../../../../app/Console/Kernel.php:16` registra `newsletter:send` sem argumento para toda segunda às 08:00. Sem argumento, o comando usa `NewsletterContent::latest_edition()`, que retorna o diretório de maior ordem sob `public/content/newsletter/`. Como hoje é sábado (03/10/2026), **o próximo disparo é segunda, 05/10/2026, às 08:00** — e é esta edição 01 que será enviada.

Decisões editoriais confirmadas com o usuário:

| Questão | Decisão |
|---|---|
| Qual edição | Regerar a **01** (não criar a 02) |
| Seções opcionais | **Nenhuma** — apenas a estrutura base de 8 seções |
| Posts destacados | Abertura da série + 2 destaques (ids 58, 64, 62) |
| Conteúdo já escrito | Aproveitar o que é atemporal; trocar só os posts |

---

## 2. Contrato de formato (apurado no código)

O template em `../../../specs/newsletter/TechPulse_template_newsletter_edicao_01.md` traz, no topo, duas linhas de metadados (`**Assunto do e-mail:**` e `**Preview text:**`). **Elas não podem ir para o arquivo gerado**, por dois motivos verificados no código:

1. `NewsletterContent::body_html()` é apenas `Str::markdown(self::body_markdown(...))` — **não há parsing algum**. Tudo que estiver no arquivo vira HTML dentro do e-mail.
2. O assunto **não vem do arquivo**. Vem de `NewsletterTranslations::for($lang)['mail_newsletter_subject']` (`../../../../app/Support/NewsletterTranslations.php:21`) e é fixo por idioma: pt-BR → `TechPulse #{edition} — O Pulsar da Tecnologia`. O `{edition}` é substituído pelo **nome do diretório**. **Não existe mecanismo de preview text no projeto.**

Ou seja: o arquivo deve conter **só conteúdo**, começando pelo H1. O formato da edição anterior já estava correto — só o conteúdo dos posts estava velho. O assunto do e-mail não é controlável por este arquivo.

---

## 3. O que foi feito

### 3.1 Seção "Do blog TechPulse" — substituída

Os três posts foram lidos direto do banco de produção via `Post::publicado()` (escopo em `../../../../app/Models/Post.php:37`), com slugs capturados verbatim:

| Post | Slug | `published_at` |
|---|---|---|
| Céticos da IA: como ler uma crítica sem engolir o tom | `ceticos-ia-como-ler-critica-sem-engolir-tom` | 2026-09-16 |
| Cory Doctorow e a Degradação das Plataformas de IA | `cory-doctorow-e-degradacao-plataformas-ia` | 2026-09-17 |
| Arvind Narayanan e o Óleo de Cobra Digital | `arvind-narayanan-e-oleo-cobra-digital` | 2026-09-17 |

Os resumos partem do campo `excerpt` do banco, reescritos para 1–2 linhas. Foi adicionada uma frase de abertura ("O blog estreou a série **Céticos da IA**") para dar coesão ao conjunto.

### 3.2 Seção "🧰 Spotlight de ferramenta" — removida

Era a única seção opcional da edição anterior (conteúdo sobre Laravel Pint). Removida conforme a decisão de usar apenas a estrutura base. O `---` que a separava foi removido junto, sem deixar separador órfão.

### 3.3 Seções mantidas

Preservadas integralmente por serem atemporais, conforme decisão do usuário:

- `## 👋 Abertura pessoal` (100–150 palavras, conforme `../../../specs/newsletter/estrutura_conteudo.md`)
- `## 💡 Dica técnica da semana` — `preventLazyLoading` no `AppServiceProvider`
- `## 🔍 Curadoria da semana` — os 3 links (Laravel Reverb, Vite 6, RFC 8058)
- `## 🛠️ Por trás do código` — o bastidor do UUID vs. slug decorativo nas imagens de conteúdo
- `## 🔁 Antes de fechar` — assinatura de Nataniel Fiuza

### 3.4 Seção "💬 Sua vez" — ajuste leve de tema

A pergunta passou de "Qual stack você está utilizando no seu projeto principal hoje?" para "Você usa IA no seu dia a dia de desenvolvimento — e em que momento você desconfia dela?", conectando com o tema da série destacada nesta edição. O restante do texto (convite a responder o e-mail) permaneceu igual.

### 3.5 Arquivos

| Arquivo | Ação |
|---|---|
| `../../../../public/content/newsletter/01/newsletter_body.md` | Reescrito |

**Não** foi criado `newsletter_body.pt_br.md`: o arquivo base já é o fallback universal em `NewsletterContent::body_markdown()`, e criar a variante apenas adicionaria um arquivo redundante que passaria a ter precedência para leitores pt-BR.

---

## 4. Correção relevante: a premissa dos "slugs quebrados"

Durante o planejamento, a hipótese era de que os links da edição anterior estivessem **quebrados** (`inteligncia-artificial-revolu-tecnolgica`, `tica-na-inteligncia-artificial` — slugs sem a transliteração correta dos acentos). **A verificação mostrou que essa hipótese estava errada:**

| URL | Status |
|---|---|
| `/post/show/inteligncia-artificial-revolu-tecnolgica` | **200** |
| `/post/show/machine-learning-fundamentos` | **200** |
| `/post/show/tica-na-inteligncia-artificial` | **200** |
| `/post/show/slug-que-nao-existe-controle-12345` (controle) | **404** |

Consulta ao banco confirmou o motivo: o slug armazenado para o post id 1 é de fato `inteligncia-artificial-revolu-tecnolgica` (`published_at` = **2024-12-25**). Os links resolviam — o slug é "feio", não inválido.

**O problema real era a desatualização, não links quebrados.** Os posts da edição anterior são de dezembro de 2024, enquanto o conteúdo recente do blog é a série de setembro de 2026. O controle com slug inexistente retornando 404 confirma que as rotas de post realmente validam o slug, e não que a aplicação devolve 200 para qualquer caminho.

---

## 5. Testes e Validações

### 5.1 Word count

```bash
wc -w public/content/newsletter/01/newsletter_body.md
# 619
```

Dentro da faixa de **400–700 palavras** exigida pelo template para as primeiras edições (o valor do `wc` inclui as URLs, portanto é conservador).

### 5.2 Estrutura e ausência de metadados

- Nenhuma ocorrência de `Assunto do e-mail`, `Preview text` ou `Notas de uso do template` no arquivo.
- Nenhuma ocorrência de `localhost` ou `127.0.0.1` (regra do `CLAUDE.md`).
- As 8 seções obrigatórias presentes, na ordem correta, sem a seção de Spotlight:

```
1:# TechPulse Newsletter — Edição #01
3:## 👋 Abertura pessoal
15:## 💡 Dica técnica da semana
32:## 📝 Do blog TechPulse
44:## 🔍 Curadoria da semana
54:## 🛠️ Por trás do código
64:## 💬 Sua vez
72:## 🔁 Antes de fechar
```

### 5.3 Renderização

```bash
php artisan tinker --execute="echo App\Support\NewsletterContent::body_html('01');"
```

Saída com exit code 0 e 5187 bytes de HTML. Verificado no HTML gerado:

- `<h1>TechPulse Newsletter — Edição #01</h1>` e os 8 `<h2>` das seções.
- Os 3 `href` dos posts apontando para `https://tech-pulse.natanfiuza.dev.br/post/show/{slug}` com os slugs corretos.
- O bloco de código PHP do `preventLazyLoading` renderizado (`php` fence reconhecido).

### 5.4 Links de produção

Verificação por HTTP status, com slug inexistente como controle:

| URL | Status |
|---|---|
| `/post/show/ceticos-ia-como-ler-critica-sem-engolir-tom` | **200** |
| `/post/show/cory-doctorow-e-degradacao-plataformas-ia` | **200** |
| `/post/show/arvind-narayanan-e-oleo-cobra-digital` | **200** |
| `/post/show/slug-que-nao-existe-controle-12345` (controle) | **404** |

Tentativa inicial via `WebFetch` retornou conteúdo vazio para as três URLs — comportamento esperado, já que o front é uma SPA Inertia e o HTML servido não carrega o texto do post. A verificação por código de status com controle negativo é conclusiva.

### 5.5 Não executado

`php artisan newsletter:send 01` **não foi rodado**, deliberadamente: o comando dispara e-mail real para todos os assinantes ativos (`NewsletterSubscriber::active()`) e grava `newsletter/last_sent.txt`. O envio acontece pelo agendador na segunda, 05/10/2026, às 08:00. Para um ensaio prévio, o caminho seguro é `MAIL_MAILER=log` no `.env`.

---

## 6. Observações e pendências

1. **O assunto do e-mail não reflete o gancho editorial.** A skill `generate-newsletter-body` orienta um assunto do tipo `TechPulse #{edition} — [Gancho principal]`, mas `NewsletterTranslations` fixa o texto em `TechPulse #01 — O Pulsar da Tecnologia` para todos os envios. Alinhar isso exigiria alterar `NewsletterTranslations` ou o mailable — **fora do escopo desta tarefa**, registrado aqui como decisão de código pendente.
2. **O H1 `# TechPulse Newsletter — Edição #01` é renderizado dentro do corpo do e-mail** como `<h1>`, logo abaixo do header do template blade (`../../../../resources/views/emails/newsletter.blade.php`), que já exibe o badge "Edição #01". Há duplicação visual do número da edição. É o comportamento que a edição anterior já tinha; mantido por consistência, mas é um candidato a limpeza futura.
3. **Aviso de lint (MD036).** O markdownlint sinaliza `**O dia em que um UUID salvou nossa integridade de dados**` como ênfase usada no lugar de heading. É o padrão do próprio template e já existia na versão anterior — mantido.
4. **Próximas edições:** criar `public/content/newsletter/02/` fará `latest_edition()` passar a devolver `02`, redirecionando automaticamente o envio de segunda para a edição nova.
