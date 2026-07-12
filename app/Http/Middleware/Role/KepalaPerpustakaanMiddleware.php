<?php

namespace App\Http\Middleware\Role;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KepalaPerpustakaanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('kepala_perpustakaan')) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Kepala Perpustakaan.');
        }

        return $next($request);
    }
}
