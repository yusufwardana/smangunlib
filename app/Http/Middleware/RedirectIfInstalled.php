<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika aplikasi SUDAH di-install dan user mencoba akses rute installer
        if (file_exists(storage_path('app/installed'))) {
            return redirect('/login')->with('error', 'Aplikasi sudah di-install.');
        }

        return $next($request);
    }
}
