<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->role;
        $roleValue = is_object($role) && property_exists($role, 'value') ? $role->value : $role;

        abort_unless($user && in_array($roleValue, $roles, true), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
