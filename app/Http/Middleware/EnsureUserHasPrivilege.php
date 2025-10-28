<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPrivilege
{
    /**
     * Handle an incoming request and ensure the authenticated user has the given privilege.
     */
    public function handle(Request $request, Closure $next, string $privilege): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasPrivilege($privilege)) {
            abort(403);
        }

        return $next($request);
    }
}
