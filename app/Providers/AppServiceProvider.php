<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrasi Policy modul Theme Manager
        Gate::policy(\App\Models\ThemeSetting::class, \App\Policies\ThemePolicy::class);

        // Registrasi Policy modul Pengaturan Hak Akses Menu (RBAC)
        Gate::policy(\App\Models\Menu::class, \App\Policies\MenuPermissionPolicy::class);

        // Super Admin secara implisit mendapat seluruh akses (Gate interception)
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Contoh Custom Gate lainnya
        Gate::define('waive-denda', function ($user) {
            return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
        });
        
        Gate::define('akses-laporan-akreditasi', function ($user) {
            return $user->hasAnyRole(['super_admin', 'kepala_sekolah', 'kepala_perpustakaan']);
        });
    }
}
