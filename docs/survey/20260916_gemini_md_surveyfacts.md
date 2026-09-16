# Survey — GEMINI.md sincronizado e porte dos agentes para `.gemini/`

**Data:** 16 de setembro de 2026
**Task:** criar o `GEMINI.md` do `tech-pulse-blog` sincronizado com o `CLAUDE.md`, usando como modelo o `GEMINI.md` do repo irmão `artigos_tech_pulse`
**Método:** sessão de grilling (`/grill-with-docs`) em 5 rodadas, 11 decisões; leitura integral do repo irmão (contrato, `GEMINI.md`, `ARCHITECTURE.md`, os dois `skill_running.md`, a skill de artigo e o survey anterior); pesquisa documental sobre o Gemini CLI e o Antigravity; e verificação de **cada** afirmação factual dos arquivos envolvidos contra a configuração do repositório (`package.json`, `tailwind.config.js`, `routes/web.php`, ADR-0001, árvore de arquivos)

---

## 1. Contexto da tarefa

O pedido apontava para um arquivo específico: o `GEMINI.md` do repo irmão `artigos_tech_pulse`. Ler o modelo foi o primeiro passo, e ele **mudou o pedido**: o `GEMINI.md` de lá não copia o contrato — ele se declara substituído e aponta para o `CLAUDE.md`. O motivo está registrado no próprio repo irmão: uma versão anterior espelhava as regras editoriais e divergiu do que os artigos de fato fazem, a ponto de exigir seções que "aparecem em 1 e 0 artigos respectivamente".

Ou seja: "sincronizado com o `CLAUDE.md`" não significa "cópia do `CLAUDE.md`". Significa um arquivo que **não tem como divergir**.

Este repositório estava pior que o irmão no ponto de partida: o runtime Gemini não recebia contrato, nem glossário, nem as regras de layout/admin. Mais grave: os dois arquivos que ele passaria a ler (`.github/agents/*.agent.md`) afirmavam coisas que a configuração do repositório desmente (§3.5).

### Estado inicial medido

| Métrica | Valor |
| --- | --- |
| Arquivos de contexto na raiz | 2 (`CLAUDE.md`, `CONTEXT.md`) |
| Arquivos que o Gemini/Antigravity leria | **zero** |
| Diretório `.gemini/` | **não existia** |
| Agentes em `.github/agents/` | 2 (layout e admin) |
| Defeitos factuais verificados nos agentes | **6 classes**, em 4 arquivos |
| Links quebrados no `CLAUDE.md` | 2 linhas (`doc/` em vez de `docs/`) |
| `docs/survey/` | **não existia** |

---

## 2. Decisões tomadas

Onze decisões foram apresentadas ao autor como alternativas, em 5 rodadas.

| # | Decisão | Escolha | Alternativas descartadas |
| --- | --- | --- | --- |
| 1 | Forma do `GEMINI.md` | **Import `@` + prosa de apontamento (híbrido)** | Ponteiro puro; espelho (cópia do contrato) |
| 2 | Escopo | **`GEMINI.md` e `.gemini/` com os dois agentes portados** | Só o `GEMINI.md` — *era a recomendação, o autor ampliou* |
| 3 | Links quebrados `doc/` → `docs/` | **Corrigir** | Deixar como está; só registrar |
| 4 | `.github/agents/` | **Manter os dois runtimes** | Migrar tudo para `.gemini/` |
| 5 | `CONTEXT.md` | **Inlinar junto com o `CLAUDE.md`** | Só o `CLAUDE.md` |
| 6 | Sincronia do par | **Corpo duplicado + `diff` documentado** | Ponteiro/symlink; geração por script |
| 7 | Defeitos factuais dos agentes | **Corrigir nos dois lados** | Corrigir só no lado novo |
| 8 | Idioma do corpo portado | **Inglês, verbatim** | Traduzir para pt-BR |
| 9 | Identificador das skills | **`snake_case`** (`tech_pulse_layout`) | kebab-case; reaproveitar o nome do Copilot |
| 10 | `CLAUDE.md` citar as skills novas | **Não** | Citar na seção Frontend — *era a recomendação, o autor recusou* |
| 11 | `CLAUDE.md:12` ("Bootstrap 5") | **Corrigir** | Deixar |

### Justificativa da decisão 1 — por que híbrido, e não ponteiro puro

O modelo do irmão é ponteiro puro. A pesquisa documental mostrou que o Gemini CLI resolve `@./arquivo.md` **inline no contexto** (§3.2), o que permitiria uma sincronia sem cópia e sem possibilidade de divergência. Por isso o desenho escolhido foi híbrido: import **e** prosa.

Mas a prosa **não é redundância decorativa**. O evidência do irmão (§3.3) mostra que o runtime-alvo real — o Antigravity — não tem comprovação de seguir ponteiros. O import é um recurso do **Gemini CLI**; o alvo aqui é o **`agy`**. Se o import não resolver, a prosa ainda instrui o agente a abrir os dois arquivos. **A prosa é que carrega o desenho**; o import é o caminho feliz.

### Justificativa da decisão 6 — por que duplicar o corpo

Consequência direta de §3.3: se uma skill reduzida a ponteiro pode chegar ao agente sem instruir nada, a skill precisa **se auto-instruir**. Uma skill que diz "veja o arquivo X" pode chegar vazia; uma skill que contém as regras, não. Custo aceito: duas fontes que precisam de edição dupla — mitigado por um comando `diff` publicado **dentro dos próprios arquivos**.

### Justificativa da decisão 8 — por que não traduzir

O `diff` de verificação é byte a byte. Traduzir um dos lados transformaria a checagem mecânica em comparação de dois textos escritos independentemente — exatamente o modo de falha que a decisão 6 existe para evitar. Reverter é barato, desde que nos dois lados.

---

## 3. Fatos levantados

### 3.1 O modelo do repo irmão: um ponteiro que se recusa a copiar

O `GEMINI.md` de `artigos_tech_pulse` tem **três blocos e nenhuma regra própria**: cabeçalho, declaração de que o contrato é o `CLAUDE.md`, e o apontamento. Ele não duplica o contrato, e o motivo está documentado no repo: a versão anterior duplicava e divergiu.

O que se aproveitou não foi o texto, foi o **princípio**.

### 3.2 O import `@` do Gemini CLI

O `GEMINI.md` é o arquivo de contexto carregado automaticamente pelo Gemini CLI, em descoberta hierárquica: global (`~/.gemini/GEMINI.md`), raiz do projeto e ancestrais até o `.git`, e subdiretórios respeitando `.gitignore`/`.geminiignore`. Os conteúdos são concatenados e enviados a cada prompt.

Sobre o import (`@./arquivo.md`), as regras que importam para este desenho:

| Propriedade | Comportamento |
| --- | --- |
| O que acontece | a linha é **substituída in place** pelo conteúdo resolvido do arquivo |
| Extensão | apenas `.md` |
| Recursão | sim; profundidade máxima **5** (default); ciclo detectado automaticamente |
| Escopo | restrito à **raiz do projeto** (`validateImportPath`) |
| Exclusão | imports dentro de **code fences** ou de crases inline são **ignorados** |
| Configuração | `context.importFormat` (`tree` default / `flat`); `context.fileName` pode trocar o nome do arquivo |

**Duas consequências de forma, deliberadas no arquivo criado:**

1. As linhas `@./CLAUDE.md` e `@./CONTEXT.md` ficam **fora de code fences e de crases**. Escrevê-las dentro de um bloco de exemplo — o reflexo natural ao documentar a sintaxe — as desativaria silenciosamente.
2. O `GEMINI.md` **não duplica nada**. Não há como divergir do `CLAUDE.md` porque não há segunda cópia das regras.

### 3.3 A fronteira entre contrato e skill

O achado que separou o desenho em dois. O `docs/ARCHITECTURE.md` do repo irmão registra:

> *"não há evidência de que o `agy` siga ponteiros para outro arquivo — uma skill reduzida a um ponteiro pode chegar ao agente sem instruir nada."*

Isso produz duas regras opostas, e a distinção não é de gosto:

| Tipo de arquivo | Estratégia | Por quê |
| --- | --- | --- |
| **Contrato** (`GEMINI.md`) | **aponta** | arquivo de contexto: o agente o lê e age sobre ele |
| **Skill** (`.gemini/skill/*/SKILL.md`) | **duplica** | precisa se auto-instruir, ou chega vazia |

### 3.4 A convenção `.gemini/skill/` (singular) e o `SKILL.md`

Confirmada nos dois `skill_running.md` do repo irmão:

| Item | Convenção |
| --- | --- |
| Diretório | `.gemini/skill/<nome>/SKILL.md` — **singular** (o Claude Code usa `skills/`, plural) |
| Invocação | `agy run <nome> "<descrição da mudança>"` |
| Listagem | `agy list --skills` |
| Logs | `~/.gemini/antigravity/logs/` |
| Frontmatter | **`---` na linha 1** — um título antes dele faz a skill perder `name` e `description` |
| Chaves | só `name` e `description` |

O defeito do frontmatter já havia sido registrado no survey anterior do repo irmão, não foi descoberto aqui — foi **evitado por leitura**.

### 3.5 Os defeitos factuais do repositório

Seis classes de afirmação falsa, nos dois agentes do Copilot. Como o `GEMINI.md` manda ler esses arquivos, corrigi-los faz parte da entrega: apontar para um mapa com links mortos materializa exatamente a divergência que o modelo do irmão existe para matar.

| # | Onde | Dizia | Realidade |
| --- | --- | --- | --- |
| 1 | layout:14-15, admin:14 | `doc/prototipos/versao_3/...` | a pasta é `docs/` — a "fonte de verdade" declarada apontava para diretório inexistente |
| 2 | layout:32, admin:29 | `app.css`, `home.css`, `article.css`, `sass/custom.scss` são "legacy Bootstrap — do not use" | **nenhum dos quatro existe**; o Bootstrap foi removido |
| 3 | layout:31, admin:28 | `corePlugins.preflight` está **desligado** | `tailwind.config.js` **não tem** a chave → preflight no default, **ligado** |
| 4 | admin:15, admin:44 | `Components/MarkdownEditor.vue` | o caminho real é `Components/Admin/MarkdownEditor.vue` |
| 5 | admin:35 | `categories.update` PUT `/update/{uuid}` | o param é `{category}` |
| 6 | admin:51 | "componentes **novos**: `ImageDropzone.vue`, `TagInput.vue`" | já existem em `Components/Admin/` |

Todas foram corrigidas **nos dois lados** (decisão 7), para que o `diff` continue significando "os runtimes estão alinhados".

### 3.6 A evidência de cada defeito

O defeito 3 é o mais instrutivo, porque o arquivo **descrevia o mundo anterior a uma migração que terminou**:

| Verificação | Resultado |
| --- | --- |
| `bootstrap` em `package.json` | ausente |
| `node_modules/bootstrap` | não existe |
| Conteúdo de `resources/css/` | só `tailwind.css` |
| `resources/sass/` | não existe |
| `resources/js/bootstrap.js` | apenas o scaffold axios do Laravel |
| `corePlugins` em `tailwind.config.js` | chave **ausente** → preflight no default (**habilitado**) |
| ADR-0001 | planejava religar o preflight "na Fase 5 (remoção do Bootstrap)" |

A migração Bootstrap → Tailwind terminou; os agentes continuavam descrevendo o estado intermediário. Não era texto meramente desatualizado — era uma instrução **falsa e invertida** ("não confie no reset do Tailwind" quando o reset está ativo).

Os demais defeitos foram verificados contra `routes/web.php` (param `{category}` na rota de update de categoria) e contra a árvore de arquivos (existência dos componentes e dos protótipos).

### 3.7 Limites de verificação — declarados, não contornados

| Verificação | Situação |
| --- | --- |
| `agy list --skills` (Antigravity) | **não executável** — o `agy` não está instalado nesta máquina |
| Import `@` em execução | **não executado** — o Gemini CLI 0.42.0 está instalado, mas exige confiar a pasta do projeto (`gemini skills list` imprimiu *"Skipping project agents due to untrusted folder"*) e abrir sessão interativa (`/memory show`). Não se rodou prompt headless (`-p`) para não gastar chamada de API sem pedido |
| Frontmatter e `diff` | **executados** — ver §6 |

Consequência registrada com honestidade: **o lado Antigravity não é verificável nesta máquina**, e o import foi conferido contra a documentação, não em execução. É por isso que existe a prosa de apontamento (§2, decisão 1) — o desenho não depende de o import funcionar.

### 3.8 Uma divergência de caminho entre os dois runtimes

Descoberta ao tentar verificar: o **Gemini CLI** descobre skills em `~/.gemini/skills/` (**plural**), enquanto `.gemini/skill/` (**singular**) é a convenção do **Antigravity**. São runtimes diferentes com convenções diferentes.

As duas skills criadas servem o **Antigravity**. Se o autor quiser que o Gemini CLI também as veja, seria uma **terceira cópia** em `.gemini/skills/` — não decidido, fora do escopo, registrado em §5.

---

## 4. Artefatos produzidos

| Arquivo | Ação |
| --- | --- |
| `GEMINI.md` | **criado** — 13 linhas, nenhuma regra própria: cabeçalho, 2 imports, prosa de rede |
| `.gemini/skill/tech_pulse_layout/SKILL.md` | **criado** — frontmatter, invocação, corpo idêntico ao agente Copilot |
| `.gemini/skill/tech_pulse_admin/SKILL.md` | **criado** — idem |
| `.github/agents/tech-pulse-layout.agent.md` | **alterado** — 3 defeitos (4 linhas) + bloco `## Sincronia` |
| `.github/agents/tech-pulse-admin.agent.md` | **alterado** — 6 defeitos (7 linhas) + bloco `## Sincronia` |
| `CLAUDE.md` | **alterado** — linha 12 (`Bootstrap 5` → `Tailwind CSS v3.4`) e 62-63 (`doc/` → `docs/`) |
| `docs/claude-code/reports/develop_natan/20260916_gemini-md.md` | **criado** — relatório da tarefa (regra do `CLAUDE.md`) |
| `docs/survey/20260916_gemini_md_surveyfacts.md` | **criado** — este arquivo; `docs/survey/` criado por esta tarefa |

**Os corpos das skills foram splicados com `sed`, não redigitados** — `sed -n '/^## Scope/,$p' <agente> >> <skill>`. Redigitar convidaria a divergência de transcrição, que é justamente o defeito que o `diff` existe para detectar. `GEMINI.md`, `CLAUDE.md` e os agentes **não** foram commitados.

---

## 5. Divergências não resolvidas

1. **O import `@` não foi verificado em execução.** O runtime-alvo é o Antigravity, que não está instalado aqui; o Gemini CLI está, mas exige confiar a pasta e sessão interativa. Verificado contra a documentação, não em execução.
2. **Antigravity não verificado.** Nada do lado `agy` é conferível nesta máquina. Noutra máquina: `agy list --skills` deve listar `tech_pulse_layout` e `tech_pulse_admin`.
3. **`.gemini/skills/` (plural) vs `.gemini/skill/` (singular).** As skills servem o Antigravity. O Gemini CLI usa outro caminho (§3.8) — uma terceira cópia seria necessária, e não foi decidida.
4. **O `diff` exige edição dupla.** Preço da decisão 6, mitigado por ser um comando só, publicado dentro dos próprios arquivos.
5. **Corpo em inglês num repo pt-BR.** Escolha consciente (decisão 8); reverter é barato, desde que nos dois lados.
6. **Uma frase fica fora do trecho comparado**: o parágrafo de abertura de cada agente ("You are the TechPulse ... specialist..."). O marcador `## Scope` é robusto por ser estável; trocá-lo por esse parágrafo criaria um falso positivo silencioso se alguém reescrevesse a frase.

---

## 6. Correções feitas durante a verificação

| # | Defeito | Onde | Correção |
| --- | --- | --- | --- |
| 1 | A hipótese inicial para a forma do `GEMINI.md` tinha só três opções (ponteiro puro / espelho / híbrido) — **desconhecia que o import `@` existia** | desenho, rodada 1 | Pesquisa documental abriu a quarta opção, que virou a escolha |
| 2 | O desenho híbrido foi recomendado supondo que o import funcionaria; a evidência do irmão (§3.3) mostrou que **o Antigravity não tem comprovação de seguir ponteiros** | justificativa da decisão 1 | Registrado que **a prosa é que carrega o desenho**, não o import — o híbrido deixou de ser conveniência e passou a ser necessidade |
| 3 | O plano dizia "11 rodadas de grilling" | plano | Corrigido para "11 decisões, em 5 rodadas" |
| 4 | O relatório linkava o `GEMINI.md` do repo irmão com caminho **relativo**, que resolvia para o arquivo **deste** repositório | relatório | Trocado por caminho local, com a nota de que o irmão está fora da árvore |
| 5 | O relatório dizia "5 correções" no layout e "6 correções" no admin — a contagem real, conferida por `git diff --numstat`, é **4 e 7 linhas** (3 e 6 defeitos) | relatório | Corrigido para a contagem verificada |

O item 5 é a mesma classe de defeito que a tarefa inteira existe para matar: um documento afirmando um número que ninguém conferiu. Foi pego por `git diff --numstat` ao escrever este survey.

### Validações que passaram

| Verificação | Comando | Resultado |
| --- | --- | --- |
| Sincronia do par (o teste central) | `diff <(sed -n '/^## Scope/,$p' <agente>) <(sed -n '/^## Scope/,$p' <skill>)` | **vazio nos dois pares** — sincronizados |
| Frontmatter na linha 1 | `head -1` de cada `SKILL.md` | `---` nos dois |
| Caminhos mortos | `grep` por `doc/prototipos`, `resources/sass`, `app.css`, `home.css`, `article.css`, `Components/MarkdownEditor` | nenhum resultado |
| Afirmação do preflight | `grep "disabled"` | nenhuma ocorrência |
| Bootstrap no `CLAUDE.md` | `grep -c "Bootstrap 5"` | **0** |
| Existência dos caminhos citados | conferência dos 8 caminhos | todos existem |
| Fim de linha | LF, sem CRLF | ok |

---

## 7. Follow-ups sugeridos

1. **Verificar em execução.** Abrir o repo no Gemini CLI (aceitando a confiança da pasta, que ele pede), rodar `/memory show` e confirmar que o conteúdo do `CLAUDE.md` e do `CONTEXT.md` aparece concatenado sob o `GEMINI.md`. No Antigravity, `agy list --skills`.
2. **Decidir sobre `.gemini/skills/` (plural)** — terceira cópia para o Gemini CLI, se o autor usar esse runtime além do Antigravity.
3. **Commit via gitpr** — não realizado, aguardando pedido explícito.
4. **Portar os demais agentes**, se houver: hoje só os dois de layout/admin existem em `.github/agents/`. Qualquer agente novo nasce com o mesmo problema de sincronia.
5. **Considerar um verificador automático** para o `diff` de sincronia (hook ou script), se o número de pares crescer — hoje são dois pares, e o comando publicado dentro dos arquivos basta.
