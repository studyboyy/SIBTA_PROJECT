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

        $homeRoute = $this->homeRouteFor($user);

        if (! $homeRoute) {
            abort(403, 'Role akun belum didukung.');
        }

        if ($request->routeIs($homeRoute)) {
            abort(403, 'Akses ditolak untuk role akun ini.');
        }

        return redirect()->route($homeRoute);
    }

    private function homeRouteFor($user): ?string
    {
        if ($user->hasRole('admin')) {
            return 'dashboard';
        }

        if ($user->hasRole('dosen')) {
            return 'dosen.dashboard';
        }

        if ($user->hasRole('mahasiswa')) {
            return 'mahasiswa.dashboard';
        }

        if ($user->hasRole('kaprodi') || $user->hasRole('pimpinan')) {
            return 'kaprodi.dashboard';
        }

        return null;
    }
}
