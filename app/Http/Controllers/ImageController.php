<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    /**
     * Exibe uma imagem do storage.
     *
     * @param  string  $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function show($filename)
    {

        $directory = 'public/images/'; 
        $path = storage_path('app/' . $directory . $filename);

        // Verifica se o arquivo existe no caminho construído
        if (!File::exists($path)) {
            abort(404, 'Imagem não encontrada.'); // Retorna um erro 404 se a imagem não existir
        }

        // Determina o tipo MIME da imagem para o cabeçalho Content-Type
        $mimeType = File::mimeType($path);


        return response()->file($path, ['Content-Type' => $mimeType]);

        /*
        // Alternativa com mais controle manual (geralmente não necessária com response()->file()):
        $fileContents = File::get($path);
        $response = response($fileContents);
        $response->header('Content-Type', $mimeType);
        $response->header('Content-Disposition', 'inline; filename="'.$filename.'"'); // Para exibir no navegador
        // Adicione outros cabeçalhos se necessário, como Cache-Control
        // $response->header('Cache-Control', 'public, max-age=31536000'); // Exemplo de cache de 1 ano
        return $response;
        */
    }
}
