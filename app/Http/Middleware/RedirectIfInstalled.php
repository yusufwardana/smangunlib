<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika aplikasi SUDAH di-install, blokir akses installer sepenuhnya.
        // Layer 1: file marker. Layer 2: cek apakah sudah ada user di DB.
        if (file_exists(storage_path('app/installed'))) {
            abort(403, 'Installer dinonaktifkan. Aplikasi sudah terinstal.');
        }

        // ponytail: fallback DB check — upgrade ke signed token jika perlu re-install flow
        try {
            if (\App\Models\User::exists()) {
                abort(403, 'Installer dinonaktifkan. Database sudah memiliki data.');
            }
        } catch (\Exception $e) {
            // DB belum tersedia (fresh install), lanjutkan
        }

        return $next($request);
    }
}
