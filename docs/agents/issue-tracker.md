# Issue tracker: Markdown local

Issues e specs deste repositório vivem como arquivos markdown em `.scratch/`.

## Convenções

- Uma feature por diretório: `.scratch/<feature-slug>/`
- A spec fica em `.scratch/<feature-slug>/spec.md`
- Issues de implementação: um arquivo por ticket em `.scratch/<feature-slug>/issues/<NN>-<slug>.md`, numerados a partir de `01`, nunca um único arquivo combinado de tickets
- O estado de triagem é registrado em uma linha `Status:` perto do topo de cada arquivo de issue
- Comentários e histórico da conversa são anexados no fim do arquivo sob o cabeçalho `## Comments`

## Quando uma skill diz "publish to the issue tracker"

Crie um novo arquivo sob `.scratch/<feature-slug>/` (criando o diretório se necessário).

## Quando uma skill diz "fetch the relevant ticket"

Leia o arquivo no caminho referenciado. O usuário normalmente passa o caminho ou o número da issue diretamente.

## Operações de wayfinding

Usadas pelo `/wayfinder`. O **mapa** é um arquivo com um arquivo **filho** por ticket.

- **Mapa**: `.scratch/<effort>/map.md` (o corpo Notes / Decisions-so-far / Fog).
- **Ticket filho**: `.scratch/<effort>/issues/NN-<slug>.md`, numerados a partir de `01`, com a pergunta no corpo. Uma linha `Type:` registra o tipo do ticket (`research`/`prototype`/`grilling`/`task`); uma linha `Status:` registra `claimed`/`resolved`.
- **Bloqueio**: linha `Blocked by: NN, NN` perto do topo. Um ticket está desbloqueado quando todos os arquivos que lista estão `resolved`.
- **Frontier**: escaneie `.scratch/<effort>/issues/` em busca de arquivos abertos, desbloqueados e não reclamados; o de menor número vence.
- **Claim**: defina `Status: claimed` e salve antes de qualquer trabalho.
- **Resolve**: anexe a resposta sob um cabeçalho `## Answer`, defina `Status: resolved` e então anexe um ponteiro de contexto (gist + link) ao Decisions-so-far do mapa em `map.md`.
