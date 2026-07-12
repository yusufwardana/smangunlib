<?php

namespace App\Policies;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BukuPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan', 'pustakawan', 'guru', 'siswa', 'kepala_sekolah']);
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
    public function update(User $user, Buku $buku): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan', 'pustakawan']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Buku $buku): bool
    {
        return $user->hasAnyRole(['super_admin', 'kepala_perpustakaan']); // Hanya kepsek dan admin yang bisa menghapus
    }
}
