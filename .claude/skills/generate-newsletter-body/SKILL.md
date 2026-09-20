---
name: generate-newsletter-body
description: Gera o corpo da edição semanal da newsletter TechPulse (public/content/newsletter/{edition}/newsletter_body.md) a partir dos posts publicados no banco de dados e do template oficial, com suporte a seções opcionais (Spotlight de ferramenta, enquetes, bastidores de projeto). Use quando o usuário pedir para gerar, criar ou montar uma nova edição da newsletter.
---

# Gerar Corpo da Newsletter TechPulse

Esta skill orienta a geração do corpo em Markdown para uma edição da newsletter semanal do TechPulse (`public/content/newsletter/{edition}/newsletter_body.md`), baseando-se nos posts publicados no banco de dados e na estrutura editorial definida em `docs/specs/newsletter/estrutura_conteudo.md` e `docs/specs/newsletter/TechPulse_template_newsletter_edicao_01.md`.

---

## 1. Fluxo de Execução

### Passo 1: Determinar o Número da Edição
1. Verifique as edições já existentes em `public/content/newsletter/`.
2. Se existirem edições anteriores (ex: `01`, `02`), incremente para a próxima edição formatada com 2 dígitos (ex: `02`, `03`).
3. Se for a primeira edição, use `01`.
4. Caso o usuário especifique uma edição (ex: "Gere a edição 02"), utilize a informada.

### Passo 2: Coletar os Posts Cadastrados no Banco de Dados
1. Obtenha os posts publicados mais recentes usando o modelo `App\Models\Post`:
   ```bash
   php artisan tinker --execute="echo App\Models\Post::publicado()->latest('published_at')->take(5)->get(['id', 'title', 'slug', 'excerpt'])->toJson(JSON_PRETTY_PRINT);"
   ```
2. Selecione entre 2 a 3 posts mais relevantes da última semana para compor a seção **Do blog TechPulse**.
3. Obtenha a URL canônica de cada post: `https://tech-pulse.natanfiuza.dev.br/post/show/{slug}` (ou `/post/show/{slug}`).

### Passo 3: Montar a Estrutura Semanal Obrigatória
A estrutura base segue rigorosamente `TechPulse_template_newsletter_edicao_01.md`:

1. **Assunto & Preview Text**:
   - Assunto: `TechPulse #{edition} — [Gancho principal da edição]` (Ex: `TechPulse #01 — a primeira edição chegou 🚀`)
   - Preview text: Resumo em 1 linha chamando a atenção para a dica técnica e o post principal.

2. **👋 Abertura pessoal**:
   - Parágrafo assinado pelo autor (Nataniel), contextualizando o momento, aprendizados recentes ou motivação da edição (100 a 150 palavras).

3. **💡 Dica técnica da semana**:
   - Dica rápida, prática e aplicável em menos de 5 minutos (Laravel, Vue 3, Docker, SQL, Git ou boas práticas de código) com snippet de código explicativo.

4. **📝 Do blog TechPulse**:
   - Lista dos 2-3 posts selecionados no Passo 2, contendo título em negrito, resumo objetivo de 1-2 linhas e link direto para leitura completa:
     `- **[Título do Post]** — [Resumo de 1-2 linhas]. [Leia o artigo completo](https://tech-pulse.natanfiuza.dev.br/post/show/{slug})`

5. **🔍 Curadoria da semana**:
   - 3 links comentados sobre novidades relevantes no ecossistema dev (artigos externos, novidades do PHP/Laravel, Vue, IA para devs).

6. **🛠️ Por trás do código**:
   - 2 a 4 parágrafos curtos narrando um bastidor técnico real: decisão de arquitetura, refatoração de código, bug inesperado ou trade-off enfrentado.

7. **💬 Sua vez**:
   - Pergunta aberta e direta convidando o leitor a responder ao e-mail.

8. **🔁 Antes de fechar**:
   - CTA para indicação da newsletter a colegas desenvolvedores e assinatura pessoal com links.

---

## 2. Seções Opcionais / Quinzenais / Mensais

Quando solicitado pelo usuário ou conforme a frequência editorial planejada, inclua as seguintes seções especiais:

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
2. Salve o arquivo gerado em:
   - `public/content/newsletter/{edition}/newsletter_body.md`
   - `public/content/newsletter/{edition}/newsletter_body.pt_br.md` (opcionalmente cópia ou base em português)
3. Garanta que o Markdown esteja limpo, sem classes CSS externas e com links funcionais.
4. Para testar o envio após a geração, utilize:
   ```bash
   php artisan newsletter:send {edition}
   ```

