---
name: generate-newsletter-body
description: Gera o corpo da edição semanal da newsletter TechPulse (public/content/newsletter/{edition}/newsletter_body.md) a partir dos posts publicados no banco de dados, traduz o corpo para todos os idiomas suportados (en, pt_pt, es, fr) e oferece suporte a seções opcionais (Spotlight de ferramenta, enquetes, bastidores de projeto). Use quando o usuário pedir para gerar, criar, montar ou traduzir uma edição da newsletter.
---

# Gerar Corpo da Newsletter TechPulse

Esta skill orienta a geração do corpo em Markdown para uma edição da newsletter semanal do TechPulse (`public/content/newsletter/{edition}/newsletter_body.md`), a tradução desse corpo para todos os idiomas suportados, e a estrutura editorial definida em `docs/specs/newsletter/estrutura_conteudo.md` e `docs/specs/newsletter/TechPulse_template_newsletter_edicao_01.md`.

---

## 1. Fluxo de Execução

### Passo 1: Determinar o Número da Edição
1. Verifique as edições já existentes em `public/content/newsletter/`.
2. Se existirem edições anteriores (ex: `01`, `02`), incremente para a próxima edição formatada com 2 dígitos (ex: `02`, `03`).
3. Se for a primeira edição, use `01`.
4. Caso o usuário especifique uma edição (ex: "Gere a edição 02"), utilize a informada.

### Passo 2: Coletar os Posts Cadastrados no Banco de Dados

> ⚠️ **O `.env` aponta para o banco de PRODUÇÃO** (`srv1062.hstgr.io` / `u147216022_tech_pulse`). Execute **apenas leitura**: `SELECT` via `tinker`. Nunca rode `migrate`, `db:seed`, `db:wipe` ou qualquer escrita a partir desta skill. Se a consulta precisar de dados que não existem, pare e pergunte ao usuário.

1. Obtenha os posts publicados mais recentes usando o modelo `App\Models\Post`:
   ```bash
   php artisan tinker --execute="echo App\Models\Post::publicado()->latest('created_at')->take(10)->get(['id', 'title', 'slug', 'excerpt', 'created_at', 'published_at'])->toJson(JSON_PRETTY_PRINT);"
   ```

2. **Ordene por `created_at`, nunca por `published_at`.** Esta é a armadilha mais fácil de cair nesta skill:

   O `published_at` é **nullable** e, na prática, vários posts publicados ficam com ele **nulo** (o model considera publicado quem tem `status = 'publicado'`, e o `scopePublicado` aceita `published_at IS NULL`). O **MySQL ordena NULL por último** em `ORDER BY ... DESC` — então um `latest('published_at')` joga justamente os artigos mais novos para o fim da lista, e o `take(5)` **nunca os mostra**.

   Foi exatamente isso que aconteceu na edição 01: os posts mais recentes do banco (ids 68, 67, 66, 65, 63, 60) têm `published_at` nulo e foram invisíveis para a consulta antiga, que devolveu posts de dezembro de 2024.

   A Home usa `created_at DESC` ([HomeController.php:22](../../../app/Http/Controllers/HomeController.php#L22)) — é a ordenação canônica do site e a que a newsletter deve espelhar.

3. O `scopePublicado` já filtra `status = 'publicado'` e respeita agendamento (`published_at IS NULL OR published_at <= now()`). Não é preciso filtrar status na mão.

4. Selecione entre 2 a 4 posts recentes para compor a seção **Do blog TechPulse**.

5. **Confirme que cada link responde 200 antes de publicar a edição.** O front é uma SPA Inertia, então `WebFetch` devolve HTML sem texto — valide por status HTTP e use um slug inexistente como controle negativo:
   ```bash
   curl -o /dev/null -s -w "%{http_code} {url_effectively_requested}\n" https://tech-pulse.natanfiuza.dev.br/post/show/{slug}
   ```

6. Obtenha a URL canônica de cada post: `https://tech-pulse.natanfiuza.dev.br/post/show/{slug}`.

### Passo 3: Montar a Estrutura Semanal Obrigatória

> **Assunto do e-mail:** o assunto **não** sai deste arquivo Markdown. Ele vem fixo de `NewsletterTranslations::for($lang)['mail_newsletter_subject']` (`TechPulse #{edition} — ...`, um por idioma). Não existe mecanismo de preview text na pipeline. Escreva o corpo sabendo disso — o gancho editorial não aparece na caixa de entrada hoje.

A estrutura base segue rigorosamente `TechPulse_template_newsletter_edicao_01.md`. Escreva em **pt-BR** (`newsletter_body.md`); a tradução é o Passo 4.

1. **👋 Abertura pessoal**:
   - Parágrafo assinado pelo autor (Nataniel), contextualizando o momento, aprendizados recentes ou motivação da edição (100 a 150 palavras).

2. **💡 Dica técnica da semana**:
   - Dica rápida, prática e aplicável em menos de 5 minutos (Laravel, Vue 3, Docker, SQL, Git ou boas práticas de código) com snippet de código explicativo.

3. **📝 Do blog TechPulse**:
   - Lista dos 2-4 posts selecionados no Passo 2, contendo título em negrito, resumo objetivo de 1-2 linhas e link direto para leitura completa:
     `- **[Título do Post]** — [Resumo de 1-2 linhas]. [Leia o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/{slug})`

4. **🔍 Curadoria da semana**:
   - 3 links comentados sobre novidades relevantes no ecossistema dev (artigos externos, novidades do PHP/Laravel, Vue, IA para devs).

5. **🛠️ Por trás do código**:
   - 2 a 4 parágrafos curtos narrando um bastidor técnico real: decisão de arquitetura, refatoração de código, bug inesperado ou trade-off enfrentado.

6. **💬 Sua vez**:
   - Pergunta aberta e direta convidando o leitor a responder ao e-mail.

7. **🔁 Antes de fechar**:
   - CTA para indicação da newsletter a colegas desenvolvedores e assinatura pessoal com links.

O corpo final deve ficar entre **400 e 700 palavras**.

---

### Passo 4: Traduzir o Corpo para Todos os Idiomas

`NewsletterTranslations::LANGS` define os idiomas suportados: **`pt_br`, `en`, `pt_pt`, `es`, `fr`**. O assinante escolhe o idioma no cadastro (coluna `lang`) e o `newsletter:send` entrega o corpo do idioma dele. Sem o arquivo traduzido, ele recebe o texto em português.

**Gere os cinco arquivos:**

| Arquivo | Idioma | Papel |
|---|---|---|
| `newsletter_body.md` | pt-BR | **Fonte de verdade e fallback universal** |
| `newsletter_body.en.md` | Inglês | tradução |
| `newsletter_body.pt_pt.md` | Português (Portugal) | tradução |
| `newsletter_body.es.md` | Espanhol | tradução |
| `newsletter_body.fr.md` | Francês | tradução |

**Não crie `newsletter_body.pt_br.md`.** A resolução em `NewsletterContent::body_markdown()` é:

```
newsletter_body.{lang}.md  →  newsletter_body.md  →  newsletter_body.pt_br.md  →  newsletter_body.en.md
```

O arquivo base (`newsletter_body.md`) é o **segundo** da cadeia — é ele que serve de fallback para qualquer idioma sem tradução própria. Se o pt-BR fosse movido para `newsletter_body.pt_br.md`, um assinante `de` ou `it` ficaria sem corpo nenhum. Criar os dois seria duplicação com risco de divergirem.

**Regras de tradução:**

- **Traduza a prosa**: abertura, dicas, resumos dos posts, curadoria, bastidores, CTA e a assinatura.
- **Não traduza os títulos dos posts.** São títulos reais de artigos publicados, com slug correspondente; traduzi-los faria o link não bater com o que o leitor encontra na página. O mesmo vale para caminhos de código, nomes de função (`preventLazyLoading`, `with()`) e trechos entre crases.
- **Preserve a voz**: informal, primeira pessoa, em português de Portugal use "partilhar" (não "compartilhar"), "bases de dados" (não "bancos"), "ficheiros" (não "arquivos").
- **Mantenha a estrutura idêntica**: mesmos headings com os mesmos emojis, mesmos separadores `---`, mesmos links absolutos, mesmo bloco de código.
- **Cada arquivo entre 400 e 700 palavras.**
- Os headings podem e devem ser traduzidos (`## 💡 Dica técnica da semana` → `## 💡 Tip of the week` / `## 💡 Astuce technique de la semaine`).

**Verificação de que cada idioma resolve para o arquivo certo:**

```bash
php artisan tinker --execute="foreach (App\Support\NewsletterTranslations::LANGS as \$l) { \$c = App\Support\NewsletterContent::body_markdown('{edition}', \$l); echo str_pad(\$l, 6) . ' => ' . str_pad(strlen(\$c), 6) . ' bytes | ' . strtok(\$c, \"\n\") . PHP_EOL; }"
```

Cada idioma deve devolver um tamanho e um H1 coerentes com o próprio arquivo — se `en` e `pt_br` tiverem exatamente o mesmo tamanho, o fallback está sendo usado e falta o arquivo traduzido.

---

## 2. Seções Opcionais / Quinzenais / Mensais

Quando solicitado pelo usuário ou conforme a frequência editorial planejada, inclua as seguintes seções especiais. Elas entram **antes** de "💬 Sua vez" e devem ser traduzidas junto com o resto no Passo 4.

### 🧰 Spotlight de Ferramenta (Frequência sugerida: Quinzenal)
- Destaque para uma lib, pacote Composer/NPM, CLI ou ferramenta de produtividade.
- Estrutura:
  ```markdown
  ---

  ## 🧰 Spotlight de ferramenta

  **[Nome da ferramenta/lib]**

  [O que ela resolve, por que começou a usar e um exemplo de uso rápido em 3-4 linhas.]
  ```

### 📊 Enquetes ou Perguntas Aprofundadas (Frequência sugerida: Quinzenal ou Mensal)
- Pergunta técnica estruturada com opções para gerar debate e coletar dados para a próxima edição.
- Estrutura:
  ```markdown
  ---

  ## 📊 Enquete da comunidade

  **[Tema em debate, ex: Qual estratégia de cache você prioriza no Laravel?]**
  1. Redis / Memcached
  2. Cache em memória / Octane
  3. Database Cache
  4. Nenhum / consultas diretas

  Responda a este e-mail com a sua opção e o motivo!
  ```

### 🏗️ Bastidores de Projeto Maior (Frequência sugerida: Mensal)
- Relato detalhado sobre a concepção, desafios arquiteturais e métricas de um projeto real ou refatoração profunda.
- Estrutura:
  ```markdown
  ---

  ## 🏗️ Bastidores de projeto: [Nome do Projeto/Desafio]

  [Narrativa estruturada explicando o contexto do problema, a solução adotada, os trade-offs considerados e o resultado prático obtido.]
  ```

---

## 3. Gravação e Verificação

1. Crie o diretório `public/content/newsletter/{edition}/` se não existir.
2. Salve os **cinco** arquivos do Passo 4 na mesma pasta: `newsletter_body.md` (pt-BR) mais `newsletter_body.{en,pt_pt,es,fr}.md`.
3. Garanta que o Markdown esteja limpo, sem classes CSS externas e com links funcionais.
4. Rode os testes de envio por idioma (eles usam `Mail::fake()`, nenhum e-mail real sai):
   ```bash
   phpunit tests/Feature/NewsletterSendCommandTest.php
   ```

> ⚠️ **Não rode `php artisan newsletter:send {edition}` para testar.** Com o `.env` atual isso dispara e-mail real para os assinantes de verdade e grava `storage/app/newsletter/last_sent.txt`, marcando a edição como enviada. O agendador (`Kernel.php`) já dispara sozinho às segundas-feiras, 08:00, na maior edição da pasta. Para conferir o resultado, renderize o HTML sem enviar:
> ```bash
> php artisan tinker --execute="echo App\Support\NewsletterContent::body_html('{edition}', 'en');"
> ```

5. **As traduções são geradas por IA e vão para a caixa de entrada sob o nome do Nataniel.** Sinalize no relatório que elas precisam de revisão humana antes do disparo — em especial o `pt_pt`, onde uma tradução ruim é mais perceptível para o leitor do que a ausência dela.
