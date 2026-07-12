<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeminjamanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan', 'pustakawan']);
    }

    /**
     * Determine whether the user can view the specific model.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        // Pustakawan/Admin bisa melihat semua
        if ($user->hasAnyRole(['super_admin', 'kepala_perpustakaan', 'pustakawan'])) {
            return true;
        }

        // Siswa/Guru hanya bisa melihat transaksinya sendiri
        return $user->anggota && $user->anggota->id === $peminjaman->anggota_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan', 'pustakawan']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Peminjaman $peminjaman): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan', 'pustakawan']);
    }
}
