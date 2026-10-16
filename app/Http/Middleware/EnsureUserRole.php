<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /** Usage : ->middleware('role:admin') ou 'role:atelier,association' */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès réservé.');
        }

        $hasEnumRole = in_array($user->role?->value, $roles, true);
        $hasSpatieRole = $user->hasAnyRole($roles);

        if (! $hasEnumRole && ! $hasSpatieRole) {
            abort(403, 'Accès réservé.');
        }

        return $next($request);
    }
}
