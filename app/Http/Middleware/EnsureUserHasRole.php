<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Garante que o usuário autenticado possua um dos papéis exigidos.
     *
     * @param  string  ...$roles  Papéis permitidos, ex.: 'admin' ou 'autor,admin'.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! $request->user()->possui_papel(...$roles)) {
            abort(403);
        }

        return $next($request);
    }
}
