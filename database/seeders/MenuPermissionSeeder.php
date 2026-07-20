<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuPermission;
use App\Services\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * MenuPermissionSeeder
 *
 * Menyiapkan seluruh data modul "Pengaturan Hak Akses Menu":
 *  1. Membuat 7 role RBAC (super_admin s/d guest).
 *  2. Membangun pohon menu (tree) sesuai struktur pada spesifikasi.
 *  3. Mendaftarkan aksi/hak akses (view, create, dst) untuk tiap menu.
 *  4. Menjalankan PermissionService::rebuild() untuk membuat permission Spatie.
 *  5. Memberikan default permission untuk tiap role sesuai contoh spesifikasi.
 *
 * Idempotent — memakai firstOrCreate/updateOrCreate sehingga aman diulang dan
 * tidak menimpa data yang sudah dikustomisasi Super Admin.
 */
class MenuPermissionSeeder extends Seeder
{
    /**
     * Definisi pohon menu. Format tiap node:
     *   [key, title, icon, route|null, [actions...], [children...]]
     * Jika actions kosong, default = ['view'].
     */
    private function menuDefinitions(): array
    {
        $crud = ['view', 'create', 'edit', 'delete'];
        $crudExport = array_merge($crud, ['export_pdf', 'export_excel', 'import_excel', 'print']);

        return [
            ['dashboard', 'Dashboard', 'fa-solid fa-grid-2', 'dashboard', ['view'], []],
            ['landing', 'Landing Page', 'fa-solid fa-house', 'landing', ['view', 'edit'], []],

            ['administrasi', 'Administrasi', 'fa-solid fa-folder-open', null, ['view'], [
                ['administrasi.sop', 'SOP', 'fa-solid fa-file-lines', null, $crudExport, []],
                ['administrasi.tata_tertib', 'Tata Tertib', 'fa-solid fa-scroll', null, $crudExport, []],
                ['administrasi.struktur', 'Struktur Organisasi', 'fa-solid fa-sitemap', null, $crudExport, []],
            ]],

            ['koleksi', 'Koleksi', 'fa-solid fa-book', 'koleksi.buku.index', ['view'], [
                ['koleksi.buku', 'Buku', 'fa-solid fa-book', 'koleksi.buku.index', $crudExport, []],
                ['koleksi.kategori', 'Kategori', 'fa-solid fa-tags', null, $crud, []],
                ['koleksi.rak', 'Rak', 'fa-solid fa-layer-group', null, $crud, []],
                ['koleksi.penulis', 'Penulis', 'fa-solid fa-user-pen', null, $crud, []],
                ['koleksi.penerbit', 'Penerbit', 'fa-solid fa-building', null, $crud, []],
                ['koleksi.inventaris', 'Inventaris', 'fa-solid fa-boxes-stacked', null, $crudExport, []],
            ]],

            ['sirkulasi', 'Sirkulasi', 'fa-solid fa-right-left', 'sirkulasi.index', ['view'], [
                ['sirkulasi.peminjaman', 'Peminjaman', 'fa-solid fa-hand-holding', 'sirkulasi.index', array_merge($crud, ['approve', 'print']), []],
                ['sirkulasi.pengembalian', 'Pengembalian', 'fa-solid fa-rotate-left', null, array_merge($crud, ['approve', 'print']), []],
                ['sirkulasi.reservasi', 'Reservasi', 'fa-solid fa-calendar-check', null, array_merge($crud, ['approve']), []],
                ['sirkulasi.denda', 'Denda', 'fa-solid fa-money-bill-wave', null, array_merge($crud, ['approve', 'print']), []],
                ['sirkulasi.pengunjung', 'Pengunjung', 'fa-solid fa-user-check', null, array_merge($crud, ['export_excel']), []],
            ]],

            ['literasi', 'Literasi', 'fa-solid fa-book-open-reader', 'gls.dashboard', array_merge($crud, ['export_pdf']), []],
            ['sarpras', 'Sarpras', 'fa-solid fa-chair', null, $crud, []],
            ['dokumen', 'Dokumen', 'fa-solid fa-file', 'dokumen.index', array_merge($crud, ['download', 'upload']), []],
            ['laporan', 'Laporan', 'fa-solid fa-chart-pie', 'laporan.index', ['view', 'export_pdf', 'export_excel', 'print'], []],

            ['sistem', 'System Management', 'fa-solid fa-gear', null, ['view'], [
                ['sistem.backup', 'Backup', 'fa-solid fa-database', 'system.backup', ['view', 'create', 'download'], []],
                ['sistem.restore', 'Restore', 'fa-solid fa-clock-rotate-left', null, ['view', 'upload'], []],
                ['sistem.update', 'Update', 'fa-solid fa-cloud-arrow-up', 'system.update', ['view', 'upload'], []],
                ['sistem.setting', 'Setting', 'fa-solid fa-sliders', 'system.settings.index', ['view', 'edit'], []],
                ['sistem.theme', 'Theme', 'fa-solid fa-palette', 'system.theme.index', ['view', 'edit'], []],
                ['sistem.user', 'User', 'fa-solid fa-users', null, $crud, []],
                ['sistem.role', 'Role', 'fa-solid fa-user-shield', null, $crud, []],
                ['sistem.permission', 'Menu Permission', 'fa-solid fa-user-lock', 'system.permissions.index', ['view', 'edit'], []],
                ['sistem.activity_log', 'Activity Log', 'fa-solid fa-list-check', null, ['view', 'export_excel'], []],
                ['sistem.audit_log', 'Audit Log', 'fa-solid fa-shield-halved', 'system.permissions.audit', ['view', 'export_excel'], []],
            ]],
        ];
    }

    public function run(): void
    {
        // 1. Pastikan seluruh role RBAC ada.
        foreach (array_keys(PermissionService::roles()) as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Bangun pohon menu + aksi.
        $sort = 0;
        foreach ($this->menuDefinitions() as $node) {
            $this->createMenu($node, null, $sort);
            $sort++;
        }

        // 3. Buat permission Spatie dari definisi menu.
        $service = app(PermissionService::class);
        $service->rebuild();

        // 4. Default permission per role sesuai contoh spesifikasi.
        $this->assignDefaultRolePermissions();

        $service->clearCache();
    }

    /**
     * Buat satu node menu beserta submenu dan aksinya secara rekursif.
     */
    private function createMenu(array $node, ?int $parentId, int $sort): void
    {
        [$key, $title, $icon, $route, $actions, $children] = $node;

        $menu = Menu::updateOrCreate(
            ['key' => $key],
            [
                'parent_id' => $parentId,
                'title'     => $title,
                'icon'      => $icon,
                'route'     => $route,
                'sort'      => $sort,
                'is_active' => true,
            ]
        );

        $actions = $actions ?: ['view'];
        $labels = PermissionService::actions();

        foreach ($actions as $action) {
            MenuPermission::updateOrCreate(
                ['menu_id' => $menu->id, 'action' => $action],
                ['label' => $labels[$action] ?? ucfirst($action)]
            );
        }

        $childSort = 0;
        foreach ($children as $child) {
            $this->createMenu($child, $menu->id, $childSort);
            $childSort++;
        }
    }

    /**
     * Berikan default permission untuk tiap role sesuai contoh pada spesifikasi.
     * Super Admin tidak perlu diberi permission eksplisit (lolos via Gate::before).
     */
    private function assignDefaultRolePermissions(): void
    {
        $grants = [
            'kepala_sekolah' => [
                'dashboard.view', 'landing.view',
                'laporan.view', 'laporan.export_pdf', 'laporan.export_excel', 'laporan.print',
                'literasi.view',
            ],
            'kepala_perpustakaan' => [
                'dashboard.view', 'landing.view',
                'koleksi.view', 'koleksi.buku.view', 'koleksi.buku.create', 'koleksi.buku.edit', 'koleksi.buku.delete',
                'koleksi.kategori.view', 'koleksi.rak.view', 'koleksi.penulis.view', 'koleksi.penerbit.view',
                'sirkulasi.view', 'sirkulasi.peminjaman.view', 'sirkulasi.peminjaman.approve',
                'sirkulasi.pengembalian.view', 'sirkulasi.denda.view', 'sirkulasi.denda.approve',
                'literasi.view',
                'laporan.view', 'laporan.export_pdf', 'laporan.export_excel', 'laporan.print',
                'sistem.view', 'sistem.setting.view',
            ],
            'pustakawan' => [
                'dashboard.view',
                'koleksi.view', 'koleksi.buku.view', 'koleksi.buku.create', 'koleksi.buku.edit',
                'koleksi.kategori.view', 'koleksi.rak.view', 'koleksi.penulis.view', 'koleksi.penerbit.view',
                'koleksi.inventaris.view',
                'sirkulasi.view', 'sirkulasi.peminjaman.view', 'sirkulasi.peminjaman.create',
                'sirkulasi.pengembalian.view', 'sirkulasi.pengembalian.create',
                'sirkulasi.reservasi.view', 'sirkulasi.denda.view', 'sirkulasi.pengunjung.view',
                'literasi.view',
                'laporan.view',
            ],
            'guru' => [
                'dashboard.view',
                'koleksi.view', 'koleksi.buku.view',
                'literasi.view',
            ],
            'siswa' => [
                'dashboard.view',
                'koleksi.view', 'koleksi.buku.view',
                'literasi.view',
            ],
            'guest' => [
                'landing.view',
            ],
        ];

        foreach ($grants as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                continue;
            }

            // Hanya berikan permission yang benar-benar ada (hasil rebuild).
            $existing = \Spatie\Permission\Models\Permission::whereIn('name', $permissions)
                ->pluck('name')
                ->all();

            // Gabungkan dengan permission lama agar kustomisasi tidak hilang.
            $merged = array_values(array_unique(array_merge(
                $role->permissions->pluck('name')->all(),
                $existing
            )));

            $role->syncPermissions($merged);
        }
    }
}
