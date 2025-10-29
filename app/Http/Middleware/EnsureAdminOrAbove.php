<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrAbove
{
    /**
     * Handle an incoming request by ensuring the authenticated user is an admin or super admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdminOrAbove()) {
            abort(403);
        }

        return $next($request);
    }
}
