<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

/**
 * Regras das imagens embutidas no markdown do post.
 *
 * O upload é imediato (colar/arrastar no editor), então o arquivo existe no
 * disco antes de o post ser salvo. A verdade sobre quais imagens um post usa é
 * o próprio markdown: não há tabela de registro, e a limpeza acontece
 * comparando os uuids referenciados antes e depois de um save.
 */
class ImagensDeConteudo
{
    /**
     * Caminho público das imagens de conteúdo.
     *
     * O `{slug}` da URL é decorativo e ignorado na leitura — ver
     * ContentImageController::show().
     */
    public const ROTA = 'post/content/images';

    /**
     * Disco usado para ler e gravar os arquivos.
     *
     * Explícito, e não o default de config('filesystems.default'), para que
     * leitura e escrita fiquem sempre no mesmo lugar e para que
     * Storage::fake('local') consiga interceptar nos testes.
     */
    public const DISCO = 'local';

    /**
     * Slug usado quando o post ainda não tem título.
     */
    public const SLUG_VAZIO = 'sem-titulo';

    /**
     * Pasta das imagens, relativa à raiz do disco.
     */
    public function diretorio(): string
    {
        return trim((string) config('techpulse.imagens_conteudo'), '/');
    }

    /**
     * Monta a URL absoluta de uma imagem de conteúdo.
     *
     * A URL gravada no markdown é absoluta (com o domínio de produção) porque
     * o app Flutter consome a API e recebe o conteúdo pronto; o frontend troca
     * a origem pela do navegador na hora de renderizar.
     *
     * @param  string  $titulo  Título atual do post, usado só para compor o slug decorativo.
     * @param  string  $uuid  Uuid da imagem.
     * @return string A URL absoluta da imagem.
     */
    public function url_absoluta(string $titulo, string $uuid): string
    {
        return rtrim((string) config('techpulse.url_publica'), '/')
            .'/'.self::ROTA
            .'/'.$this->slug_do_titulo($titulo)
            .'/'.$uuid;
    }

    /**
     * Extrai os uuids das imagens de conteúdo referenciadas num markdown.
     *
     * A extração casa pelo caminho da rota, nunca pelo domínio: o conteúdo do
     * banco pode ter a URL absoluta de produção, uma relativa, ou um domínio
     * antigo de antes de uma migração — todas contam como a mesma referência.
     *
     * @param  string|null  $conteudo  Markdown do post.
     * @return array<int, string> Uuids únicos, em minúsculas.
     */
    public function uuids_referenciados(?string $conteudo): array
    {
        if ($conteudo === null || trim($conteudo) === '') {
            return [];
        }

        $padrao = '#/'.preg_quote(self::ROTA, '#').'/[^/\s)"\'<>]*'
            .'/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})#i';

        preg_match_all($padrao, $conteudo, $achados);

        return array_values(array_unique(array_map('strtolower', $achados[1])));
    }

    /**
     * Remove do disco as imagens que saíram do texto entre dois saves.
     *
     * @param  Post  $post  Post que está sendo salvo.
     * @param  string|null  $conteudo_anterior  Markdown antes da edição.
     * @param  string|null  $conteudo_novo  Markdown depois da edição.
     */
    public function sincronizar(Post $post, ?string $conteudo_anterior, ?string $conteudo_novo): void
    {
        $removidos = array_diff(
            $this->uuids_referenciados($conteudo_anterior),
            $this->uuids_referenciados($conteudo_novo)
        );

        foreach ($removidos as $uuid) {
            $this->remover_se_orfa($post, $uuid);
        }
    }

    /**
     * Remove do disco todas as imagens de conteúdo referenciadas pelo post.
     *
     * Usado quando o post é excluído. Deve ser chamado depois do delete, para
     * que o próprio post não conte como referência de si mesmo.
     *
     * @param  Post  $post  Post excluído (o conteúdo ainda está em memória).
     */
    public function remover_todas(Post $post): void
    {
        foreach ($this->uuids_referenciados($post->content) as $uuid) {
            $this->remover_se_orfa($post, $uuid);
        }
    }

    /**
     * Apaga o arquivo de uma imagem, desde que nenhum outro post a use.
     *
     * A mesma imagem pode estar referenciada em mais de um post (nada impede
     * copiar o markdown de uma para outra), então apagar sem checar deixaria o
     * outro post com uma imagem quebrada.
     *
     * @param  Post  $post  Post de origem da remoção.
     * @param  string  $uuid  Uuid da imagem.
     */
    private function remover_se_orfa(Post $post, string $uuid): void
    {
        $caminho = $this->diretorio().'/'.$uuid;
        $disco = Storage::disk(self::DISCO);

        // Sem arquivo em disco não há o que apagar (e evita a consulta).
        if (! $disco->exists($caminho)) {
            return;
        }

        // Outro post ainda referencia a imagem: o arquivo fica.
        if ($this->esta_em_uso($uuid, $post->id)) {
            return;
        }

        $disco->delete($caminho);
    }

    /**
     * Verifica se algum outro post referencia o uuid informado.
     *
     * A primeira condição estreita o conjunto para posts que usam imagens de
     * conteúdo; a segunda é a referência em si. As duas são LIKE com curinga à
     * esquerda, portanto sem índice possível — aceitável no volume do blog.
     *
     * @param  string  $uuid  Uuid da imagem.
     * @param  int  $post_id  Id do post que está sendo salvo ou excluído.
     * @return bool True se outro post ainda usa a imagem.
     */
    private function esta_em_uso(string $uuid, int $post_id): bool
    {
        return Post::query()
            ->where('id', '!=', $post_id)
            ->where('content', 'like', '%'.self::ROTA.'%')
            ->where('content', 'like', '%'.$uuid.'%')
            ->exists();
    }

    /**
     * Gera o slug decorativo da URL a partir do título do post.
     *
     * @param  string  $titulo  Título do post (pode estar vazio no create).
     * @return string O slug, ou SLUG_VAZIO quando o título não gera nenhum.
     */
    private function slug_do_titulo(string $titulo): string
    {
        // criar_slug('') devolve '' — e uma URL com slug vazio não casaria com
        // a rota, deixando a imagem inacessível para sempre.
        return criar_slug($titulo) ?: self::SLUG_VAZIO;
    }
}
