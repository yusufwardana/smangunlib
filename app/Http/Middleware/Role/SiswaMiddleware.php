<?php

namespace App\Http\Middleware\Role;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiswaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('siswa')) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Siswa.');
        }

        return $next($request);
    }
}
