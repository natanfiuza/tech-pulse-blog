<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Lê e gerencia as edições da newsletter armazenadas em
 * public/content/newsletter/{edition}/newsletter_body.{lang}.md.
 */
final class NewsletterContent
{
    /**
     * Detecta a edição mais recente disponível em public/content/newsletter.
     */
    public static function latest_edition(): ?string
    {
        $base = public_path('content/newsletter');

        if (! File::isDirectory($base)) {
            return null;
        }

        $directories = File::directories($base);

        if (empty($directories)) {
            return null;
        }

        $editions = array_map('basename', $directories);
        natsort($editions);

        return end($editions) ?: null;
    }

    /**
     * Carrega o Markdown do corpo da newsletter para o idioma especificado,
     * aplicando fallback para o arquivo base caso a tradução não exista.
     *
     * @throws RuntimeException quando nenhuma edição for encontrada.
     */
    public static function body_markdown(string $edition, string $lang = 'pt_br'): string
    {
        $normalizedLang = NewsletterTranslations::normalize($lang);
        $base = public_path("content/newsletter/{$edition}/newsletter_body");

        $pathsToTry = [
            "{$base}.{$normalizedLang}.md",
            "{$base}.md",
            "{$base}.pt_br.md",
            "{$base}.en.md",
        ];

        foreach ($pathsToTry as $path) {
            if (File::exists($path)) {
                return File::get($path);
            }
        }

        throw new RuntimeException("Corpo da newsletter não encontrado para a edição {$edition}.");
    }

    /**
     * Converte o Markdown da newsletter em HTML formatado.
     */
    public static function body_html(string $edition, string $lang = 'pt_br'): string
    {
        return Str::markdown(self::body_markdown($edition, $lang));
    }

    /**
     * Retorna a última edição enviada gravada no arquivo marcador.
     */
    public static function last_sent_edition(): ?string
    {
        if (! Storage::disk('local')->exists('newsletter/last_sent.txt')) {
            return null;
        }

        return trim(Storage::disk('local')->get('newsletter/last_sent.txt')) ?: null;
    }

    /**
     * Registra a edição como enviada no arquivo marcador.
     */
    public static function mark_sent(string $edition): void
    {
        Storage::disk('local')->put('newsletter/last_sent.txt', $edition);
    }
}
