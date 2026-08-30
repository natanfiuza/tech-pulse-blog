# Relatório: Setup das Skills do Matt Pocock

**Data:** 2026-08-30
**Branch:** `develop_natan`
**Skill executada:** `/mattpocock-skills:setup-matt-pocock-skills` (v1.2.3)

## Objetivo

Configurar a integração das skills de engenharia do Matt Pocock (issue tracker, domain docs) no repositório Tech Pulse Blog.

## Exploração inicial

| Item | Estado |
|---|---|
| Remote | GitHub (`natanfiuza/tech-pulse-blog`) |
| `CLAUDE.md` | Existia, sem seção `## Agent skills` |
| `AGENTS.md` | Não existia |
| `CONTEXT.md` / `CONTEXT-MAP.md` | Não existiam |
| `docs/adr/` | Não existia |
| `docs/agents/` | Não existia |
| `.scratch/` | Não existia |
| Skill `triage` | Não instalada → seção de labels de triagem omitida |
| Sinais de monorepo | Nenhum → layout single-context |

## Decisões do usuário

1. **Issue tracker:** Markdown local (`.scratch/<feature-slug>/`) — escolha do usuário, apesar do remote GitHub
2. **Diretório dos arquivos de configuração:** `docs/agents/` (padrão da skill; consistente com `docs/claude-code/` já usado pelo CLAUDE.md)
3. **Idioma dos arquivos gerados:** português (pt-BR), conforme convenção do repositório
4. **Triage labels:** seção pulada (skill `triage` não instalada)
5. **Domain docs:** single-context (sem monorepo), sem pergunta

## Arquivos criados/editados

| Arquivo | Ação |
|---|---|
| [CLAUDE.md](../../../../CLAUDE.md) | Editado — adicionada seção `## Agent skills` (issue tracker + domain docs, sem bloco de triage) |
| [docs/agents/issue-tracker.md](../../../../docs/agents/issue-tracker.md) | Criado — convenções do tracker local markdown em `.scratch/` |
| [docs/agents/domain.md](../../../../docs/agents/domain.md) | Criado — regras de consumo da doc de domínio, layout single-context |

## Adaptações aos templates da skill

- Referência a `triage-labels.md` removida de `issue-tracker.md` (skill `triage` não instalada)
- Nota adicionada em `domain.md` esclarecendo que `src/`, `ordering/` e `billing/` dos exemplos são ilustrativos (repositório é single-context)
- Todo o conteúdo traduzido para pt-BR

## Próximos passos

- As skills que leem esses arquivos (ex.: `to-tickets`, `to-spec`, `wayfinder`) passarão a usar `.scratch/` como issue tracker
- Editar `docs/agents/*.md` diretamente quando necessário; re-rodar a skill só para trocar de tracker ou reiniciar do zero
- Opcional: instalar a skill `triage` para habilitar a seção de labels de triagem
