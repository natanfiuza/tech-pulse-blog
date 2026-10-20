<?php

namespace App\Support;

/**
 * Medição de audiência (GA4).
 *
 * O ID é lido de config/analytics.php e valores vazios desativam a medição.
 * O ID é enviado como prop compartilhada do Inertia e a tag é inserida
 * exclusivamente no cliente após o consentimento.
 */
final class Analytics
{
    /**
     * Retorna o ID do GA4 configurado, ou nulo quando a medição está desativada.
     */
    public static function ga4Id(): ?string
    {
        $id = trim((string) config('analytics.ga4_id'));

        return $id === '' ? null : $id;
    }
}
