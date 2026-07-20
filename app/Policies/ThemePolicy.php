<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * ThemePolicy
 *
 * Otorisasi akses ke modul Theme Manager. Hanya peran administratif
 * (super_admin & kepala_perpustakaan) yang boleh melihat & mengubah tema.
 * Catatan: super_admin sudah otomatis lolos via Gate::before di AppServiceProvider.
 */
class ThemePolicy
{
    use HandlesAuthorization;

    /**
     * Boleh melihat halaman Theme Manager.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }

    /**
     * Boleh memperbarui / menyimpan tema, upload aset, custom CSS/JS.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }

    /**
     * Boleh melakukan aksi destruktif (reset, import yang menimpa tema).
     */
    public function manage(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }
}
