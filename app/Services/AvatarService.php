<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    public const DISCO = 'local';

    /**
     * Paleta de cores pastéis para o fundo dos avatares padrão.
     */
    private const CORES_PASTEIS = [
        '#FDE2E4',
        '#E2ECE9',
        '#DFE7FD',
        '#FFF1E6',
        '#E2F0D9',
        '#FCE1E4',
        '#D8F3DC',
        '#FFDDD2',
        '#E0AAFF',
        '#BEE1E6',
        '#C7F9CC',
        '#FFD6A5',
        '#FDFFB6',
        '#CAFFBF',
        '#9BF6FF',
        '#A0C4FF',
        '#BDB2FF',
        '#FFC6FF',
    ];

    /**
     * Retorna a pasta das fotos de perfil relativa ao disco local.
     */
    public function diretorio(): string
    {
        return trim((string) config('techpulse.imagens_perfil', 'data/user_profile_images'), '/');
    }

    /**
     * Retorna o caminho relativo do avatar de um usuário no disco local.
     */
    public function caminho(string $uuid): string
    {
        return $this->diretorio().'/'.$uuid;
    }

    /**
     * Extrai as primeiras letras do primeiro e último nome.
     */
    public function extrair_iniciais(string $nome): string
    {
        $partes = preg_split('/\s+/', trim($nome), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($partes)) {
            return 'U';
        }

        if (count($partes) === 1) {
            return mb_strtoupper(mb_substr($partes[0], 0, 2, 'UTF-8'), 'UTF-8');
        }

        $primeira = mb_substr($partes[0], 0, 1, 'UTF-8');
        $ultima = mb_substr(end($partes), 0, 1, 'UTF-8');

        return mb_strtoupper($primeira.$ultima, 'UTF-8');
    }

    /**
     * Escolhe uma cor pastel aleatória ou determinística para o fundo.
     */
    public function cor_pastel(?string $semente = null): string
    {
        if ($semente) {
            $indice = abs(crc32($semente)) % count(self::CORES_PASTEIS);

            return self::CORES_PASTEIS[$indice];
        }

        return self::CORES_PASTEIS[array_rand(self::CORES_PASTEIS)];
    }

    /**
     * Gera o conteúdo SVG do avatar padrão com iniciais em fundo pastel e letras em preto.
     */
    public function gerar_svg_padrao(string $nome, ?string $uuid = null): string
    {
        $iniciais = htmlspecialchars($this->extrair_iniciais($nome), ENT_XML1, 'UTF-8');
        $cor_fundo = $this->cor_pastel($uuid ?? $nome);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="256" height="256">
    <rect width="256" height="256" rx="128" fill="{$cor_fundo}"/>
    <text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle"
          fill="#1A1A1A" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif"
          font-size="96" font-weight="700" letter-spacing="2">
        {$iniciais}
    </text>
</svg>
SVG;
    }

    /**
     * Salva ou restaura o avatar padrão (SVG) para o usuário especificado.
     */
    public function gerar_e_salvar_avatar_padrao(User $user): void
    {
        if (! $user->uuid) {
            $user->uuid = (string) Str::uuid();
            $user->saveQuietly();
        }

        $svg = $this->gerar_svg_padrao($user->name, $user->uuid);
        Storage::disk(self::DISCO)->put($this->caminho($user->uuid), $svg);
    }

    /**
     * Salva o avatar a partir de um upload de arquivo.
     */
    public function salvar_upload(User $user, UploadedFile $file): void
    {
        if (! $user->uuid) {
            $user->uuid = (string) Str::uuid();
            $user->saveQuietly();
        }

        Storage::disk(self::DISCO)->putFileAs(
            $this->diretorio(),
            $file,
            $user->uuid
        );
    }

    /**
     * Tenta baixar a imagem do Google e salvá-la em storage/app/data/user_profile_images/{uuid}.
     * Em caso de falha, gera o avatar padrão com as iniciais.
     */
    public function salvar_avatar_google(User $user, string $google_avatar_url): void
    {
        if (! $user->uuid) {
            $user->uuid = (string) Str::uuid();
            $user->saveQuietly();
        }

        try {
            $response = Http::timeout(10)->get($google_avatar_url);

            if ($response->successful() && ! empty($response->body())) {
                Storage::disk(self::DISCO)->put($this->caminho($user->uuid), $response->body());

                return;
            }
        } catch (\Exception $e) {
            Log::warning("Não foi possível baixar o avatar do Google para o usuário {$user->id}: ".$e->getMessage());
        }

        // Fallback: avatar padrão com iniciais
        $this->gerar_e_salvar_avatar_padrao($user);
    }

    /**
     * Verifica se o avatar existe no disco.
     */
    public function existe(string $uuid): bool
    {
        return Storage::disk(self::DISCO)->exists($this->caminho($uuid));
    }

    /**
     * Retorna o caminho absoluto do arquivo no disco.
     */
    public function caminho_absoluto(string $uuid): string
    {
        return Storage::disk(self::DISCO)->path($this->caminho($uuid));
    }

    /**
     * Retorna a URL pública para exibir o avatar do usuário.
     */
    public function url_avatar(?User $user): string
    {
        if (! $user || ! $user->uuid) {
            return '';
        }

        return route('user.avatar', ['uuid' => $user->uuid]);
    }
}
