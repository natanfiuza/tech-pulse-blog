<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Decisão do visitante sobre cookies de medição (LGPD/GDPR, ADR 0003).
 *
 * Três estados possíveis: sem cookie a decisão é nula (exibindo o banner).
 * Valores desconhecidos ou corrompidos também retornam nulo, garantindo
 * segurança e ausência de medição até nova manifestação.
 */
final class Consent
{
    public const GRANTED = 'granted';

    public const DENIED = 'denied';

    /**
     * Decisões suportadas.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return [self::GRANTED, self::DENIED];
    }

    /**
     * Valida se o valor informado é uma decisão aceita.
     */
    public static function isSupported(mixed $value): bool
    {
        return is_string($value) && in_array($value, self::values(), strict: true);
    }

    /**
     * Nome do cookie de consentimento.
     */
    public static function cookie(): string
    {
        return (string) config('analytics.cookie', 'consent');
    }

    /**
     * Validade do cookie em dias.
     */
    public static function days(): int
    {
        return (int) config('analytics.cookie_days', 180);
    }

    /**
     * Recupera a decisão válida registrada na requisição, ou nulo se não houver.
     */
    public static function fromRequest(Request $request): ?string
    {
        $cookie = $request->cookie(self::cookie());

        return is_string($cookie) && self::isSupported($cookie) ? $cookie : null;
    }

    /**
     * Verifica se o cookie foi enviado porém com formato ou valor inválido/corrompido.
     */
    public static function isCorrupt(Request $request): bool
    {
        $cookie = $request->cookie(self::cookie());

        return $cookie !== null && ! self::isSupported($cookie);
    }
}
