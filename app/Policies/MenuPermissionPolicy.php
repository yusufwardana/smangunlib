<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * MenuPermissionPolicy
 *
 * Otorisasi akses ke modul "Pengaturan Hak Akses Menu". Hanya Super Admin yang
 * boleh mengelola matriks hak akses menu. Super Admin sebenarnya sudah otomatis
 * lolos melalui Gate::before di AppServiceProvider, namun policy ini menjadi
 * lapis pertahanan kedua yang eksplisit dan mudah diuji.
 */
class MenuPermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Boleh melihat halaman pengaturan hak akses menu.
     */
    public function view(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Boleh mengubah/menyimpan matriks hak akses (sync, copy, reset).
     */
    public function manage(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
