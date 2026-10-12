# Relatório de Implementação: Corpo da Newsletter — Edição 02

- **Data:** 2026-10-12
- **Branch:** `develop_natan`
- **Tarefa:** Gerar o corpo da edição 02 da newsletter (`public/content/newsletter/02/`) em pt-BR e traduzir para todos os idiomas suportados, conforme `../../../specs/newsletter/estrutura_conteudo.md` e o template da edição 01.

---

## 1. Contexto

A edição 02 ainda não existia: `public/content/newsletter/` continha apenas o diretório `01`. A edição foi criada do zero com os cinco arquivos de idioma.

Recorte temporal: a edição 01 foi montada em 03/10/2026 com posts até **03/10**. Entre 03/10 e hoje (12/10) o blog publicou **seis** posts novos — a edição 02 cobre essa janela.

---

## 2. Coleta dos posts — o banco de produção estava inacessível

> ⚠️ **Este é o ponto que mais merece atenção no relatório.**

O passo 2 da skill manda consultar `Post::publicado()` via `tinker`. **A consulta falhou**:

```
SQLSTATE[HY000] [1045] Access denied for user 'u147216022_tech_pulse'@'177.37.142.218' (using password: YES)
```

A tentativa foi repetida ao final da execução, com o mesmo resultado — **não é falha transitória**. O MySQL de produção (`srv1062.hstgr.io`) rejeita este IP. Provavelmente a lista de IPs autorizados no painel da Hostinger não inclui o IP atual (177.37.142.218) — endereço residencial, que muda.

**Solução adotada:** usei a **API pública** do próprio site como fonte de leitura, que se mostrou equivalente à consulta pretendida. Verifiquei a equivalência no código antes de confiar nela — `../../../app/Http/Controllers/PostController.php:433-436`:

```php
$paginated_posts = Post::publicado()
    ->with('category', 'hashtags')
    ->orderBy('created_at', 'desc')
    ->paginate($per_page, ['*'], 'page', $page);
```

Ou seja: **mesmo escopo `publicado()`** (o que já cobre `status = 'publicado'` e o agendamento de `published_at`) e **mesma ordenação canônica `created_at desc`** da Home (`../../../app/Http/Controllers/HomeController.php:22`). A armadilha do `published_at` nulo descrita na skill não se aplica a esta rota, porque ela nunca ordena por essa coluna.

| Endpoint usado | Papel |
|---|---|
| `GET /api/posts/pagination?per_page=10&page=1` | Lista dos posts com `title`, `slug`, `excerpt`, `created_at`, `web_url` |

Nenhuma escrita foi feita em nenhum momento. Se o usuário quiser restabelecer o acesso direto, é preciso liberar o IP no painel da Hostinger (ou usar um túnel).

### Posts publicados na janela (ordenados por `created_at desc`)

| Data | Título | Selecionado |
|---|---|---|
| 2026-10-12 | git bisect: Encontre o Commit Exato Que Quebrou a Aplicação em Segundos | ✅ |
| 2026-10-11 | Do DevTools ao Terminal: Como Dissecar Gargalos de APIs com o cURL Nativo | ✅ |
| 2026-10-11 | A WinterCG e o Tratado de Paz dos Runtimes JavaScript | ✅ |
| 2026-10-09 | O Manual Técnico do EU AI Act: Proteção Real ou Reserva de Mercado? | ✅ |
| 2026-10-07 | O Ataque no npm que usa IA para Adaptar Malwares na Instalação | — |
| 2026-10-07 | A SAP Comprou a TechWolf: Por Que Agentes Precisam de um Context Graph | — |

Foram escolhidos os **4 mais recentes** (o teto da faixa 2–4), com boa diversidade temática: ferramenta de Git, depuração de API, ecossistema JavaScript e regulação de IA. Nenhum post repete a edição 01. Os resumos partem do campo `excerpt`, reescritos para 1–2 linhas.

---

## 3. Estrutura entregue

Estrutura base obrigatória, sem seções opcionais (Spotlight / Enquete / Bastidores de projeto maior):

| # | Seção | Conteúdo |
|---|---|---|
| 1 | 👋 Abertura pessoal | Gancho da edição: a newsletter passou a sair multilíngue |
| 2 | 💡 Dica técnica da semana | `Model::shouldBeStrict()` — evolução da dica da #01 |
| 3 | 📝 Do blog TechPulse | 4 posts selecionados |
| 4 | 🔍 Curadoria da semana | 3 links comentados |
| 5 | 🛠️ Por trás do código | O fallback de idioma da newsletter |
| 6 | 💬 Sua vez | Pergunta aberta |
| 7 | 🔁 Antes de fechar | CTA de encaminhamento + assinatura |

**Continuidade com a #01:** a dica de hoje (`shouldBeStrict`) é a evolução natural da que foi enviada na edição anterior (`preventLazyLoading`) — o texto faz essa ponte explicitamente. A seção "Por trás do código" narra a feature recém-implementada em `a6abad8` (*"feat: envia newsletter no idioma de cada assinante"*), que é um bastidor real e datado.

### Detalhe editorial apurado no código

A seção "Por trás do código" explica **por que o pt-BR não tem arquivo próprio**. A cadeia de resolução está em `../../../app/Support/NewsletterContent.php:50-55`:

```php
$pathsToTry = [
    "{$base}.{$normalizedLang}.md",
    "{$base}.md",          // <- fallback universal
    "{$base}.pt_br.md",
    "{$base}.en.md",
];
```

Como `newsletter_body.md` é o **segundo** da fila, mover o português para `newsletter_body.pt_br.md` faria um assinante de idioma não suportado (`de`, `it`) cair no `.en.md`. Mantendo o pt-BR no arquivo base, ele recebe português. O texto da newsletter reproduz esse raciocínio.

---

## 4. Traduções

Cinco arquivos gerados, sem `newsletter_body.pt_br.md` (conforme a skill — o pt-BR mora no arquivo base justamente para servir de fallback universal):

| Arquivo | Idioma | Palavras |
|---|---|---|
| `newsletter_body.md` | pt-BR (fonte) | 645 |
| `newsletter_body.en.md` | Inglês | 627 |
| `newsletter_body.pt_pt.md` | Português (Portugal) | 648 |
| `newsletter_body.es.md` | Espanhol | 684 |
| `newsletter_body.fr.md` | Francês | 689 |

Contagem de prosa, excluindo blocos de código, URLs, cabeçalhos e marcadores de lista. Todos dentro da faixa 400–700 exigida.

Regras de tradução aplicadas:

- **Títulos dos posts não traduzidos** — são títulos reais com slug correspondente; traduzi-los faria o link não bater com a página.
- **Código, nomes de função e trechos entre crases preservados.**
- **pt_pt** com léxico de Portugal: "partilhar", "ficheiros" (não "arquivos"), "ligações" (não "links"), "registo" (não "cadastro"), "aceder", "Vemo-nos".
- Headings traduzidos, mesmos emojis, mesmos separadores `---`, mesmos links absolutos.

---

## 5. Verificação

### Links dos posts (HTTP)

```
200  git-bisect-encontre-commit-exato-que-quebrou-aplicacao-segundos
200  devtools-ao-terminal-como-dissecar-gargalos-apis-curl-nativo
200  wintercg-e-tratado-paz-dos-runtimes-javascript
200  manual-tecnico-eu-ai-act-protecao-real-ou-reserva-mercado
404  slug-que-nao-existe-controle-negativo   <- controle negativo
```

O controle negativo devolveu **404**, confirmando que os 200 são respostas reais e não um soft-404 da SPA.

### Links da curadoria (HTTP)

```
200  https://git-scm.com/docs/git-bisect
200  https://wintertc.org/
200  https://digital-strategy.ec.europa.eu/en/policies/regulatory-framework-ai
```

### Resolução por idioma

```
pt_br  => 4747 bytes | # TechPulse Newsletter — Edição #02
en     => 4826 bytes | # TechPulse Newsletter — Edition #02
pt_pt  => 4756 bytes | # TechPulse Newsletter — Edição #02
es     => 4946 bytes | # TechPulse Newsletter — Edición #02
fr     => 5254 bytes | # TechPulse Newsletter — Édition #02
latest_edition: 02
```

Cada idioma devolve conteúdo próprio (tamanhos distintos), e **nenhum** devolveu o texto em português — o fallback não está sendo acionado por engano.

### Renderização HTML

```
h1=1  h2=7  ul=2  pre=1  a=9
markdown bruto restante: 0
links quebrados (href="#"): 0
```

### Testes

```
php vendor/bin/phpunit tests/Feature/NewsletterSendCommandTest.php
OK (6 tests, 17 assertions)
```

Usam `Mail::fake()` — nenhum e-mail real saiu.

> ⚠️ `phpunit` não está no PATH deste ambiente (Git Bash). O binário precisa ser chamado por `php vendor/bin/phpunit`.

> ⚠️ **Não executei `php artisan newsletter:send 02`** — com o `.env` atual isso dispararia e-mail real para os assinantes e gravaria `last_sent.txt`. A validação foi feita renderizando o HTML sem enviar.

---

## 6. Pendências e pontos de atenção

### 6.1 Revisão humana das traduções — obrigatória antes do disparo

As traduções foram geradas por IA e serão entregues **em nome do Nataniel**. Precisam de revisor humano antes do disparo, com atenção especial a:

- **`pt_pt`** — uma tradução ruim é mais perceptível para o leitor português do que a ausência dela. Vale conferir o léxico e a regência.
- **`es`** — falso cognato e o uso de "boletín"/"correo" (adotado) versus "newsletter"/"email".
- **`fr`** — o texto tem a maior contagem (689 palavras); conferir se alguma frase soou longa demais.

### 6.2 A edição 01 pode nunca ter sido enviada

- `storage/app/newsletter/last_sent.txt` **não existe** nesta máquina — mas esse arquivo é **local**, e o agendador roda no servidor. A ausência aqui **não prova** que nada foi enviado em produção.
- Hoje é **segunda-feira, 12/10/2026**. O cron (`../../../app/Console/Kernel.php:16`) dispara `newsletter:send` às 08:00 de toda segunda. Se ele rodou hoje pela manhã, enviou a **edição 01**, que era a maior da pasta naquele momento.
- **A partir de agora, o disparo semanal envia a edição 02**, porque `latest_edition()` retorna o diretório de maior ordem. Se a 01 não tiver ido hoje, **ela não será enviada** — será pulada.
- **Ação sugerida:** confirmar no servidor se `last_sent.txt` existe e o que contém. Se a 01 não saiu e o objetivo era enviá-la, é preciso decidir entre enviá-la manualmente ou aceitar o salto para a 02.

### 6.3 Acesso ao banco de produção

O IP atual não tem acesso ao MySQL. Para voltar a usar o fluxo de leitura direta previsto na skill, liberar `177.37.142.218` (ou o IP vigente) no painel da Hostinger. Vale considerar tornar isso parte do passo 2 da skill, já que a API pública provou ser um substituto fiel.

---

## 7. Arquivos criados

Todos novos — nenhum arquivo existente foi modificado.

| Arquivo | Descrição |
|---|---|
| `../../../../public/content/newsletter/02/newsletter_body.md` | Corpo pt-BR (fonte de verdade e fallback universal) |
| `../../../../public/content/newsletter/02/newsletter_body.en.md` | Tradução em inglês |
| `../../../../public/content/newsletter/02/newsletter_body.pt_pt.md` | Tradução em português de Portugal |
| `../../../../public/content/newsletter/02/newsletter_body.es.md` | Tradução em espanhol |
| `../../../../public/content/newsletter/02/newsletter_body.fr.md` | Tradução em francês |
| `20261012_newsletter_edicao_02.md` | Este relatório |

---

## 8. Observação de lint

O editor acusou `MD036/no-emphasis-as-heading` (linhas 17 e 59 dos arquivos): os subtítulos da dica e do "Por trás do código" usam `**negrito**` em vez de `##`. **Isso é intencional e consistente com a edição 01**, que usa exatamente o mesmo padrão. Alterar para heading mudaria a hierarquia visual do e-mail.

O uso de `console.log` e `dd()` é proibido pelo projeto — nenhum foi utilizado.
