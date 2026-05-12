<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfRoleMismatch
{
    /**
     * Allow access only when the authenticated user has one of the allowed roles.
     * Otherwise, return them to the previous page instead of showing a 403.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->guest(route('login'));
        }

        $allowedRoles = collect($roles)
            ->flatMap(fn(string $role) => preg_split('/[|,]/', $role, -1, PREG_SPLIT_NO_EMPTY))
            ->filter()
            ->values();

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return redirect()->back();
    }
}
