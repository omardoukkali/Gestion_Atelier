<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si l'utilisateur n'est pas connecté ou n'a pas un des rôles autorisés
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403, "Action non autorisée. Vous n'avez pas les permissions nécessaires.");
        }

        return $next($request);
    }
}
