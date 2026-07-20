<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckMenuPermission
 *
 * Middleware RBAC berbasis permission menu. Melindungi route dengan memeriksa
 * apakah pengguna login memiliki permission yang dibutuhkan. Berbeda dengan
 * middleware role (if role == admin) yang dilarang spesifikasi, middleware ini
 * murni memeriksa PERMISSION sehingga hak akses tetap dikontrol database.
 *
 * Penggunaan pada route:
 *   ->middleware('menu:koleksi.buku.view')
 *   ->middleware('menu:koleksi.buku,create')   // key + action terpisah
 *
 * Jika tidak memiliki hak akses, tampilkan halaman 403 dengan pesan
 * "Anda tidak memiliki hak akses.".
 */
class CheckMenuPermission
{
    /**
     * @param  string  ...$permissions  Satu atau beberapa permission; cukup punya salah satu.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        abort_if($user === null, 403, 'Anda tidak memiliki hak akses.');

        // Dukungan format "key,action" -> "key.action".
        $normalized = array_map(function (string $permission): string {
            return str_contains($permission, ',')
                ? str_replace(',', '.', $permission)
                : $permission;
        }, $permissions);

        foreach ($normalized as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki hak akses.');
    }
}
