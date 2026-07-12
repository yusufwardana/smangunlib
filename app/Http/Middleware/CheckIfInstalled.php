<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika aplikasi BELUM di-install dan user mencoba akses rute SELAIN installer
        if (!file_exists(storage_path('app/installed'))) {
            // Bypass jika mengakses rute installer, API, atau asset
            if (!$request->is('install*') && !$request->is('api/*') && !$request->ajax()) {
                return redirect()->route('installer.welcome');
            }
        }

        return $next($request);
    }
}
