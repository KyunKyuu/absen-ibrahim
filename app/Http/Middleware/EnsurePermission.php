<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();
        abort_if(! $user || ! collect($permissions)->contains(fn ($permission) => $user->canDo($permission)), 403);

        return $next($request);
    }
}
