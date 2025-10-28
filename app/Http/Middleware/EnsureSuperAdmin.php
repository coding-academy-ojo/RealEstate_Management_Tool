<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
  /**
   * Handle an incoming request by ensuring the authenticated user is a super admin.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->user();

    if (!$user || !$user->isSuperAdmin()) {
      abort(403);
    }

    return $next($request);
  }
}
