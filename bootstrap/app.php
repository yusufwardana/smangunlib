<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Mendaftarkan global middleware
        $middleware->web(append: [
            \App\Http\Middleware\CheckIfInstalled::class,
            \App\Http\Middleware\LoadTheme::class,
        ]);

        // Mendaftarkan alias middleware RBAC kustom
        $middleware->alias([
            'installed' => \App\Http\Middleware\RedirectIfInstalled::class,
            
            'role.super_admin' => \App\Http\Middleware\Role\SuperAdminMiddleware::class,
            'role.kepala_sekolah' => \App\Http\Middleware\Role\KepalaSekolahMiddleware::class,
            'role.kepala_perpustakaan' => \App\Http\Middleware\Role\KepalaPerpustakaanMiddleware::class,
            'role.pustakawan' => \App\Http\Middleware\Role\PustakawanMiddleware::class,
            'role.guru' => \App\Http\Middleware\Role\GuruMiddleware::class,
            'role.siswa' => \App\Http\Middleware\Role\SiswaMiddleware::class,

            // RBAC Menu Permission: cek hak akses berbasis permission menu
            'menu' => \App\Http\Middleware\CheckMenuPermission::class,
            
            // Spatie default middleware
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
