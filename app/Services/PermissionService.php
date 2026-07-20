<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuPermission;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * PermissionService
 *
 * Sumber kebenaran tunggal (single source of truth) untuk modul RBAC
 * "Pengaturan Hak Akses Menu". Bertanggung jawab atas:
 *
 *  - Membangun pohon menu (tree) dari database dan meng-cache-nya.
 *  - Membuat/menyinkronkan permission Spatie dari definisi menu (rebuild).
 *  - Memfilter menu untuk sidebar sesuai hak akses pengguna.
 *  - Memeriksa hak akses tombol/aksi (view, create, edit, dst).
 *  - Mengelola cache permission (rebuild & clear).
 *
 * Seluruh hak akses TIDAK di-hardcode; semuanya dibaca dari database dan
 * disajikan melalui cache demi performa (eager loading + rememberForever).
 */
class PermissionService
{
    /** Kunci cache untuk pohon menu penuh. */
    public const CACHE_TREE = 'rbac.menu_tree';

    /** Kunci cache untuk peta permission => menu_id. */
    public const CACHE_PERMISSION_MAP = 'rbac.permission_menu_map';

    /**
     * Seluruh aksi/hak akses yang didukung sistem beserta labelnya.
     * Menjadi acuan checkbox pada UI dan pembuatan permission.
     *
     * @return array<string,string>
     */
    public static function actions(): array
    {
        return [
            'view'          => 'View',
            'create'        => 'Create',
            'edit'          => 'Edit',
            'delete'        => 'Delete',
            'approve'       => 'Approve',
            'export_pdf'    => 'Export PDF',
            'export_excel'  => 'Export Excel',
            'import_excel'  => 'Import Excel',
            'print'         => 'Print',
            'download'      => 'Download',
            'upload'        => 'Upload',
        ];
    }

    /**
     * Peran/role yang tersedia pada sistem (RBAC).
     *
     * @return array<string,string>
     */
    public static function roles(): array
    {
        return [
            'super_admin'          => 'Super Admin',
            'kepala_sekolah'       => 'Kepala Sekolah',
            'kepala_perpustakaan'  => 'Kepala Perpustakaan',
            'pustakawan'           => 'Pustakawan',
            'tendik'               => 'Tendik',
            'guru'                 => 'Guru',
            'siswa'                => 'Siswa',
            'guest'                => 'Guest',
        ];
    }

    /* ===================================================================
     |  MENU TREE
     |=================================================================== */

    /**
     * Pohon menu penuh (root + children rekursif) dari cache.
     *
     * @return Collection<int,Menu>
     */
    public function tree(): Collection
    {
        $cached = Cache::rememberForever(self::CACHE_TREE, function () {
            return Menu::query()
                ->active()
                ->roots()
                ->with('childrenRecursive', 'menuPermissions')
                ->orderBy('sort')
                ->get();
        });

        // Guard against corrupt/stale cache (__PHP_Incomplete_Class)
        if (! $cached instanceof Collection) {
            Cache::forget(self::CACHE_TREE);

            return Menu::query()
                ->active()
                ->roots()
                ->with('childrenRecursive', 'menuPermissions')
                ->orderBy('sort')
                ->get();
        }

        return $cached;
    }

    /**
     * Peta [permission_name => menu_id] dari tabel permission_has_menu.
     * Digunakan untuk mengetahui menu mana yang "dimiliki" oleh sebuah permission.
     *
     * @return array<string,int>
     */
    public function permissionMenuMap(): array
    {
        $cached = Cache::rememberForever(self::CACHE_PERMISSION_MAP, function () {
            return Permission::query()
                ->join('permission_has_menu', 'permissions.id', '=', 'permission_has_menu.permission_id')
                ->pluck('permission_has_menu.menu_id', 'permissions.name')
                ->toArray();
        });

        // Guard against corrupt/stale cache (__PHP_Incomplete_Class)
        if (! is_array($cached)) {
            Cache::forget(self::CACHE_PERMISSION_MAP);

            return Permission::query()
                ->join('permission_has_menu', 'permissions.id', '=', 'permission_has_menu.permission_id')
                ->pluck('permission_has_menu.menu_id', 'permissions.name')
                ->toArray();
        }

        return $cached;
    }

    /* ===================================================================
     |  PENGECEKAN HAK AKSES
     |=================================================================== */

    /**
     * Apakah pengguna boleh melakukan sebuah aksi pada menu tertentu.
     * Contoh: canDo($user, 'koleksi.buku', 'create').
     */
    public function canDo(?Authenticatable $user, string $menuKey, string $action = 'view'): bool
    {
        if (! $user) {
            return false;
        }

        return $user->can($menuKey.'.'.$action);
    }

    /**
     * Apakah menu (beserta submenu-nya) layak tampil di sidebar untuk pengguna.
     * Menu tampil bila pengguna memiliki permission ".view" pada menu tsb,
     * ATAU salah satu submenunya dapat dilihat.
     */
    public function canViewMenu(?Authenticatable $user, Menu $menu): bool
    {
        if (! $user) {
            return false;
        }

        // Super admin selalu melihat seluruh menu (via Gate::before).
        if ($user->can($menu->permissionName('view'))) {
            return true;
        }

        foreach ($menu->children as $child) {
            if ($this->canViewMenu($user, $child)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pohon menu yang sudah difilter sesuai hak akses pengguna, siap dipakai
     * sidebar. URL diresolusi dari nama route (jika terdaftar) atau kolom url.
     *
     * @return array<int,array<string,mixed>>
     */
    public function sidebarFor(?Authenticatable $user): array
    {
        if (! $user) {
            return [];
        }

        return $this->buildSidebar($this->tree(), $user);
    }

    /**
     * @param  Collection<int,Menu>  $menus
     * @return array<int,array<string,mixed>>
     */
    private function buildSidebar(Collection $menus, Authenticatable $user): array
    {
        $result = [];

        foreach ($menus as $menu) {
            if (! $this->canViewMenu($user, $menu)) {
                continue;
            }

            $children = $this->buildSidebar($menu->children, $user);

            $result[] = [
                'key'      => $menu->key,
                'title'    => $menu->title,
                'icon'     => $menu->icon,
                'url'      => $this->resolveUrl($menu),
                'route'    => $menu->route,
                'children' => $children,
            ];
        }

        return $result;
    }

    /**
     * Resolusi URL sebuah menu: gunakan route bila terdaftar, lalu url manual,
     * jika tidak ada keduanya kembalikan "#".
     */
    private function resolveUrl(Menu $menu): string
    {
        if ($menu->route && Route::has($menu->route)) {
            try {
                return route($menu->route);
            } catch (\Exception $e) {
                // route requires parameters not available here
            }
        }

        return $menu->url ?: '#';
    }

    /* ===================================================================
     |  MATRIKS PERMISSION PER ROLE (untuk UI)
     |=================================================================== */

    /**
     * Kumpulan nama permission yang dimiliki sebuah role (untuk mencentang checkbox).
     *
     * @return array<int,string>
     */
    public function permissionsOfRole(Role $role): array
    {
        return $role->permissions->pluck('name')->all();
    }

    /**
     * Sinkronkan permission sebuah role berdasarkan daftar nama permission
     * yang dikirim dari form. Hanya permission yang benar-benar terdaftar pada
     * peta menu yang diproses (mencegah privilege escalation).
     *
     * @param  array<int,string>  $permissionNames
     */
    public function syncRolePermissions(Role $role, array $permissionNames): void
    {
        $valid = array_keys($this->permissionMenuMap());
        $filtered = array_values(array_intersect($permissionNames, $valid));

        DB::transaction(function () use ($role, $filtered) {
            $role->syncPermissions($filtered);
        });

        $this->clearCache();
    }

    /**
     * Salin seluruh permission dari satu role ke role lain.
     */
    public function copyPermissions(Role $source, Role $target): void
    {
        DB::transaction(function () use ($source, $target) {
            $target->syncPermissions($source->permissions);
        });
        $this->clearCache();
    }

    /**
     * Kosongkan seluruh permission sebuah role.
     */
    public function resetPermissions(Role $role): void
    {
        DB::transaction(function () use ($role) {
            $role->syncPermissions([]);
        });
        $this->clearCache();
    }

    /* ===================================================================
     |  REBUILD & CACHE
     |=================================================================== */

    /**
     * Bangun ulang seluruh permission Spatie dari definisi menu di database.
     *
     * Untuk setiap baris pada menu_permissions dibuatkan (jika belum ada)
     * satu permission bernama "{menu.key}.{action}", lalu dipetakan ke menu-nya
     * pada tabel permission_has_menu. Idempotent — aman dijalankan berkali-kali.
     */
    public function rebuild(): int
    {
        $count = 0;

        MenuPermission::query()->with('menu')->chunk(200, function ($chunk) use (&$count) {
            foreach ($chunk as $mp) {
                if (! $mp->menu) {
                    continue;
                }

                $name = $mp->menu->key.'.'.$mp->action;

                $permission = Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                );

                // Petakan permission -> menu (upsert pada primary key permission_id).
                DB::table('permission_has_menu')->updateOrInsert(
                    ['permission_id' => $permission->id],
                    ['menu_id' => $mp->menu_id, 'action' => $mp->action],
                );

                $count++;
            }
        });

        $this->clearCache();

        return $count;
    }

    /**
     * Bersihkan seluruh cache terkait permission & menu (termasuk cache Spatie).
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_TREE);
        Cache::forget(self::CACHE_PERMISSION_MAP);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
