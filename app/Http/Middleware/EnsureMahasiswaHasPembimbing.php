<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMahasiswaHasPembimbing
{
    /**
     * Lock mahasiswa feature pages until at least one supervisor is assigned.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('mahasiswa')) {
            return $next($request);
        }

        $mahasiswa = $user->mahasiswa;
        $hasPembimbing = $mahasiswa && $mahasiswa->bimbingans()->whereNotNull('dosen_id')->exists();

        if ($hasPembimbing) {
            return $next($request);
        }

        return redirect()->route('mahasiswa.dashboard');
    }
}
