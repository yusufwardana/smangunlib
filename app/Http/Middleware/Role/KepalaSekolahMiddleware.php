<?php

namespace App\Http\Middleware\Role;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KepalaSekolahMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('kepala_sekolah')) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Kepala Sekolah.');
        }

        return $next($request);
    }
}
