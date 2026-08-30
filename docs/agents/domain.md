# Domain Docs

Como as skills de engenharia devem consumir a documentação de domínio deste repositório ao explorar o codebase.

## Antes de explorar, leia estes

- **`CONTEXT.md`** na raiz do repositório, ou
- **`CONTEXT-MAP.md`** na raiz do repositório, se existir: aponta para um `CONTEXT.md` por contexto. Leia cada um relevante ao tópico.
- **`docs/adr/`**: leia ADRs que toquem a área em que você vai trabalhar. Em repositórios multi-contexto, verifique também `src/<contexto>/docs/adr/` para decisões específicas do contexto.

Se qualquer um desses arquivos não existir, **prossiga silenciosamente**. Não sinalize a ausência; não sugira criá-los de antemão. A skill `/domain-modeling` (acessada via `/grill-with-docs` e `/improve-codebase-architecture`) os cria de forma lazy quando termos ou decisões são realmente resolvidos.

## Estrutura de arquivos

Repositório single-context (a maioria):

```
/
├── CONTEXT.md
├── docs/adr/
│   ├── 0001-event-sourced-orders.md
│   └── 0002-postgres-for-write-model.md
└── src/
```

Repositório multi-contexto (presença de `CONTEXT-MAP.md` na raiz):

```
/
├── CONTEXT-MAP.md
├── docs/adr/                          ← decisões do sistema inteiro
└── src/
    ├── ordering/
    │   ├── CONTEXT.md
    │   └── docs/adr/                  ← decisões específicas do contexto
    └── billing/
        ├── CONTEXT.md
        └── docs/adr/
```

*Os diretórios `src/`, `ordering/` e `billing/` dos exemplos são apenas ilustrativos — este repositório é single-context.*

## Use o vocabulário do glossário

Quando sua saída nomear um conceito de domínio (em um título de issue, uma proposta de refactor, uma hipótese, um nome de teste), use o termo conforme definido em `CONTEXT.md`. Não derive para sinônimos que o glossário evita explicitamente.

Se o conceito que você precisa não estiver no glossário ainda, isso é um sinal: ou você está inventando linguagem que o projeto não usa (reconsidere) ou há uma lacuna real (anote para `/domain-modeling`).

## Sinalize conflitos de ADR

Se sua saída contradizer um ADR existente, sinalize explicitamente em vez de sobrescrever silenciosamente:

> _Contradiz o ADR-0007 (event-sourced orders), mas vale reabrir porque…_
