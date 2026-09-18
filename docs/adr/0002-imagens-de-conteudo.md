# Imagens de conteúdo: arquivo por uuid, slug decorativo e limpeza por diff

O editor de posts passou a aceitar imagens coladas ou arrastadas, no estilo do editor de issues do GitHub: o arquivo é enviado **no momento do paste/drop** (antes de o post ser salvo) e o markdown da imagem entra na posição do cursor. Isso cria duas obrigações que o blog não tinha: servir o arquivo por uma URL própria e **apagá-lo fisicamente** quando ele deixa de aparecer no texto.

A URL pedida embute o slug do título (`/post/content/images/{slug_do_titulo}/{uuid}`), mas o `Post::save()` regenera o slug sempre que o título muda. Renomear um post quebraria todas as imagens já gravadas nele — e o markdown delas, que está no banco, não seria reescrito.

## Considered Options

- **Arquivo por pasta de post (`{post_uuid}/{imagem_uuid}`)** — rejeitado: exigiria uma pasta temporária no `create`, quando o post ainda não existe (o upload é imediato), e nenhuma vantagem prática sobre o arquivo plano.
- **Nome do arquivo com extensão** — rejeitado: a convenção da imagem de capa já é `storage/app/public/images/{uuid}` sem extensão; manter as duas iguais evita duas regras. O MIME é sniffado na leitura.
- **URL relativa gravada no markdown** — rejeitado: o app Flutter consome a API e recebe o conteúdo pronto, sem uma origem de onde resolver o caminho relativo.
- **Domínio de produção hardcoded no código** — rejeitado: `config('app.url')` é `http://localhost:8000` no `.env` local e gravaria localhost dentro do banco.
- **Rastrear as imagens numa tabela, com limpeza por evento** — rejeitado: tabela nova, migration nova e um estado paralelo que pode divergir do texto. A verdade sobre quais imagens um post usa já está no próprio markdown.
- **Upload só no save do post (adiado)** — rejeitado: é o oposto do comportamento pedido; o autor precisa ver a imagem no preview antes de publicar.
- **Escolhidas**: arquivo plano por uuid de imagem; `{slug}` decorativo na URL (a leitura resolve só pelo `{uuid}`); URL absoluta no markdown com normalização de origem no frontend; limpeza por diff do markdown no save, com checagem de referência.

## Consequences

- **Renomear o título nunca quebra imagem**: nenhum markdown precisa ser reescrito quando o slug muda. O `{slug}` da URL fica permanentemente desatualizado em relação ao post — é o preço aceito, e o `abort_unless(Str::isUuid(...))` na leitura garante que a URL não vire vetor de path traversal.
- **A limpeza só acontece no save**: uma imagem colada e depois apagada na mesma sessão (sem nunca ter sido gravada no banco) fica no disco como órfã. Resolver isso exigiria o comando de varredura ou a tabela de registro, ambos descartados aqui.
- **A mesma imagem pode viver em dois posts** (nada impede copiar o markdown de um para o outro), então apagar checa referências antes: um `LIKE '%{uuid}%'` em `posts.content`. Sem índice possível, aceitável no volume do blog.
- **O conteúdo do banco continua sendo markdown puro** — as URLs absolutas dentro dele são só texto. Uma troca de domínio exige reescrever o conteúdo gravado, ou aceitar que a normalização de origem no frontend cobre a leitura.
- `app/Services/ImagensDeConteudo.php` passa a ser a única fonte das regras (rota, pasta, extração de uuid, limpeza); `PostController::update()` e `::destroy()` delegam para ele.
- A rota de servir a imagem é pública, sem auth — como o resto do conteúdo do blog. Uma imagem de rascunho fica acessível para quem tiver a URL; o `uuid` não é adivinhável, e o comportamento é o mesmo da imagem de capa hoje.
- Fora do escopo, por decisão: a **imagem de capa** continua sem limpeza (trocar ou remover a capa deixa órfão), e o domínio de produção segue hardcoded em `image_front_url`.
