<?php

namespace App\Http\Middleware;

use App\Services\ThemeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * LoadTheme
 *
 * Memuat seluruh konfigurasi tema (dari cache) satu kali per request dan
 * membagikannya ke seluruh Blade view melalui variabel $theme serta blok
 * CSS variables ($themeCss). Tidak melakukan query berulang berkat ThemeService.
 */
class LoadTheme
{
    public function __construct(private readonly ThemeService $theme)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Bila aplikasi belum terinstal atau tabel belum ada, jangan ganggu request.
        try {
            $all = $this->theme->all();
            $css = $this->theme->cssVariables();
        } catch (\Throwable $e) {
            $all = [];
            $css = '';
        }

        View::share('theme', $all);
        View::share('themeCss', $css);

        return $next($request);
    }
}
