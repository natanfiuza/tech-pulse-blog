<?php

namespace App\Http\Controllers;

use App\Services\ImagensDeConteudo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Imagens embutidas no conteúdo do post.
 *
 * Diferente do ImageController (que só lê a imagem de capa), este controller
 * tem as duas pontas: recebe o upload imediato do editor e serve o arquivo.
 */
class ContentImageController extends Controller
{
    /**
     * @param  ImagensDeConteudo  $imagens  Regras de caminho, URL e limpeza.
     */
    public function __construct(private ImagensDeConteudo $imagens) {}

    /**
     * Recebe uma imagem colada, arrastada ou escolhida no editor.
     *
     * O upload acontece antes de o post existir: o arquivo é gravado com nome
     * = uuid e a URL devolvida é inserida no markdown na posição do cursor. A
     * associação com o post só passa a existir quando o markdown é salvo.
     *
     * @return \Illuminate\Http\JsonResponse O uuid e a URL absoluta da imagem.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
            'title' => 'nullable|string|max:255',
        ], [
            'image.required' => __('Nenhuma imagem foi enviada.'),
            'image.image' => __('O arquivo enviado não é uma imagem válida.'),
            'image.mimes' => __('A imagem deve ser PNG, JPG ou WebP.'),
            'image.max' => __('A imagem deve ter no máximo 5MB.'),
        ]);

        $uuid = Str::uuid()->toString();

        try {
            Storage::disk(ImagensDeConteudo::DISCO)->putFileAs(
                $this->imagens->diretorio(),
                $request->file('image'),
                $uuid
            );
        } catch (\Exception $e) {
            Log::error('Erro no upload da imagem de conteúdo: '.$e->getMessage());

            return response()->json(['message' => __('Não foi possível salvar a imagem.')], 500);
        }

        return response()->json([
            'uuid' => $uuid,
            'url' => $this->imagens->url_absoluta((string) $request->input('title', ''), $uuid),
        ], 201);
    }

    /**
     * Serve uma imagem de conteúdo.
     *
     * O `$slug` é decorativo e ignorado de propósito: o slug do post é
     * regerado sempre que o título muda (Post::save()), então ele nunca é
     * autoritativo. Resolver apenas pelo uuid é o que impede que renomear um
     * post quebre todas as imagens já gravadas nele.
     *
     * @param  string  $slug  Slug do título no momento do upload (ignorado).
     * @param  string  $uuid  Uuid da imagem.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(string $slug, string $uuid)
    {
        // Fecha o universo do parâmetro antes de tocar no disco: sem isso um
        // segmento decodificado poderia escapar da pasta.
        abort_unless(Str::isUuid($uuid), 404, 'Imagem não encontrada.');

        $caminho = $this->imagens->diretorio().'/'.$uuid;
        $disco = Storage::disk(ImagensDeConteudo::DISCO);

        abort_unless($disco->exists($caminho), 404, 'Imagem não encontrada.');

        $caminho_absoluto = $disco->path($caminho);

        return response()->file($caminho_absoluto, [
            // O arquivo não tem extensão, então o tipo é detectado do conteúdo.
            'Content-Type' => File::mimeType($caminho_absoluto),
            // Seguro aqui, e só aqui: cada upload de conteúdo gera um uuid
            // novo, então o conteúdo daquele caminho é imutável por construção.
            // A imagem de capa reusa o uuid do post e é sobrescrita no
            // re-upload — não copiar este header para o ImageController.
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
