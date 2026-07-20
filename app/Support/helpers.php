<?php

use App\Services\PermissionService;
use App\Services\ThemeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

if (! function_exists('theme')) {
    /**
     * Ambil nilai konfigurasi tema.
     *
     * Contoh:
     *   theme('primary_color')          // bentuk singkat, dicari di seluruh grup
     *   theme('color.primary_color')    // bentuk penuh "group.key"
     *   theme('logo_sekolah')
     *   theme('font_family', "'Inter'") // dengan default
     *
     * @param  string|null  $key      Kunci setting. Null mengembalikan seluruh map.
     * @param  mixed         $default  Nilai default bila key tidak ditemukan.
     */
    function theme(?string $key = null, mixed $default = null): mixed
    {
        /** @var ThemeService $service */
        $service = app(ThemeService::class);

        if ($key === null) {
            return $service->all();
        }

        return $service->get($key, $default);
    }
}

if (! function_exists('theme_asset')) {
    /**
     * Ambil URL publik untuk aset tema (logo, favicon, background) yang tersimpan
     * di disk "public". Mengembalikan $default bila aset belum diunggah.
     */
    function theme_asset(string $key, ?string $default = null): ?string
    {
        $path = theme($key);

        if (! $path) {
            return $default;
        }

        // Bila sudah berupa URL absolut, kembalikan apa adanya.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}

if (! function_exists('theme_css_variables')) {
    /**
     * Kembalikan blok CSS ":root { --var: value; }" berdasarkan tema aktif.
     */
    function theme_css_variables(): string
    {
        return app(ThemeService::class)->cssVariables();
    }
}

if (! function_exists('permission_service')) {
    /**
     * Akses cepat ke PermissionService (modul RBAC Menu Permission).
     */
    function permission_service(): PermissionService
    {
        return app(PermissionService::class);
    }
}

if (! function_exists('user_can')) {
    /**
     * Pemeriksaan hak akses berbasis permission (RBAC) untuk pengguna login.
     *
     * Bungkus tipis atas Gate agar aman dipanggil untuk guest (mengembalikan
     * false tanpa exception). Digunakan di Blade untuk menampilkan/menyembunyikan
     * tombol CRUD, export, import, print, dsb.
     *
     * Contoh:
     *   @if(user_can('koleksi.buku.create')) ... @endif
     */
    function user_can(string $permission): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->can($permission);
    }
}

if (! function_exists('menu_can')) {
    /**
     * Apakah pengguna login boleh melakukan sebuah aksi pada sebuah menu.
     *
     * Contoh: menu_can('koleksi.buku', 'create')
     */
    function menu_can(string $menuKey, string $action = 'view'): bool
    {
        return permission_service()->canDo(Auth::user(), $menuKey, $action);
    }
}

if (! function_exists('sidebar_menu')) {
    /**
     * Pohon menu yang sudah difilter sesuai hak akses pengguna login.
     * Dipakai komponen sidebar agar menu otomatis mengikuti permission.
     *
     * @return array<int,array<string,mixed>>
     */
    function sidebar_menu(): array
    {
        return permission_service()->sidebarFor(Auth::user());
    }
}


