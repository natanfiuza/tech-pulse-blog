<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserProfileImageController extends Controller
{
    public function __construct(private AvatarService $avatar_service) {}

    /**
     * Serve a imagem de perfil (avatar) do usuário a partir do seu uuid.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(string $uuid, Request $request)
    {
        abort_unless(Str::isUuid($uuid), 404, 'Avatar não encontrado.');

        if (! $this->avatar_service->existe($uuid)) {
            // Se o arquivo ainda não existir, tenta encontrar o usuário e gerar
            $user = User::where('uuid', $uuid)->first();

            if (! $user) {
                abort(404, 'Avatar não encontrado.');
            }

            $this->avatar_service->gerar_e_salvar_avatar_padrao($user);
        }

        $caminho_absoluto = $this->avatar_service->caminho_absoluto($uuid);

        if (! file_exists($caminho_absoluto)) {
            abort(404, 'Avatar não encontrado.');
        }

        $primeiros_bytes = substr(ltrim(file_get_contents($caminho_absoluto, false, null, 0, 50)), 0, 5);
        $eh_svg = strtolower($primeiros_bytes) === '<svg' || str_contains($primeiros_bytes, '<?xml');

        $mime = $eh_svg ? 'image/svg+xml' : File::mimeType($caminho_absoluto);

        return response()->file($caminho_absoluto, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}

