<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Inertia\Response): (\Inertia\Response)  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse | InertiaResponse
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
