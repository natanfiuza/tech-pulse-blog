<?php

namespace App\Http\Middleware;

use App\Support\Consent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remove o cookie de consentimento do navegador caso chegue com valor inválido.
 */
class HandleConsent
{
    /**
     * Trata a requisição garantindo a limpeza de cookies corrompidos.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Consent::isCorrupt($request)) {
            $response->headers->setCookie(Cookie::forget(Consent::cookie()));
        }

        return $response;
    }
}
