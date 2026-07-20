# Modul Pengaturan Hak Akses Menu (Menu Permission / RBAC)

> **Sistem Informasi Perpustakaan SMA — Laravel 12 + Bootstrap 5 + Blade + MySQL**

---

## 1. Gambaran Umum

Modul ini mengimplementasikan **RBAC (Role Based Access Control)** penuh dimana seluruh menu, submenu, dan aksi (CRUD, export, import, print, approval, dll) dikontrol melalui **database** — tidak ada hak akses yang di-hardcode.

Super Admin dapat mengatur:

| Fitur | Keterangan |
|---|---|
| Menu yang tampil di sidebar | Otomatis mengikuti permission role |
| Submenu & tree | Unlimited depth, dikelola via database |
| Tombol CRUD | View, Create, Edit, Delete |
| Export/Import | Export PDF, Export Excel, Import Excel |
| Print & Download | Print, Download, Upload |
| Approval | Approve |
| Dashboard Widget | Mengikuti permission `dashboard.view` |
| Landing Page | Visibility per role |

---

## 2. Role yang Tersedia

| Slug | Label |
|---|---|
| `super_admin` | Super Admin |
| `kepala_sekolah` | Kepala Sekolah |
| `kepala_perpustakaan` | Kepala Perpustakaan |
| `pustakawan` | Pustakawan |
| `guru` | Guru |
| `siswa` | Siswa |
| `guest` | Guest |

> **Super Admin** secara otomatis memiliki seluruh hak akses melalui `Gate::before` di `AppServiceProvider` — tidak perlu diberi permission eksplisit.

---

## 3. Aksi/Hak Akses yang Didukung

Untuk setiap menu dapat diatur:

| Action Key | Label |
|---|---|
| `view` | View |
| `create` | Create |
| `edit` | Edit |
| `delete` | Delete |
| `approve` | Approve |
| `export_pdf` | Export PDF |
| `export_excel` | Export Excel |
| `import_excel` | Import Excel |
| `print` | Print |
| `download` | Download |
| `upload` | Upload |

---

## 4. Struktur Database

### Tabel Baru (di atas tabel Spatie bawaan)

```
menus
├── id
├── parent_id (FK → menus.id, nullable)
├── key (unique, contoh: "koleksi.buku")
├── title
├── icon
├── route (nama route Laravel)
├── url (URL manual)
├── sort
├── is_active
└── timestamps

menu_permissions
├── id
├── menu_id (FK → menus.id)
├── action (view/create/edit/…)
├── label
└── timestamps

permission_has_menu
├── permission_id (PK, FK → permissions.id)
├── menu_id (FK → menus.id)
└── action
```

### Tabel Spatie (sudah ada)

- `roles` — Daftar role
- `permissions` — Daftar permission (di-generate dari menu)
- `role_has_permissions` — Pivot role ↔ permission
- `model_has_roles` — Pivot user ↔ role

### Tabel Audit

- `audit_logs` — Riwayat perubahan permission (siapa, kapan, role, before/after)

### Migration

```
database/migrations/2026_07_12_000002_create_menu_permission_tables.php
```

---

## 5. Model

| Model | File | Keterangan |
|---|---|---|
| `Menu` | `app/Models/Menu.php` | Node pohon menu, relasi parent/children rekursif |
| `MenuPermission` | `app/Models/MenuPermission.php` | Aksi yang tersedia per menu |

---

## 6. Service

### `PermissionService` (`app/Services/PermissionService.php`)

Sumber kebenaran tunggal (single source of truth) untuk RBAC:

| Method | Keterangan |
|---|---|
| `tree()` | Pohon menu penuh dari cache |
| `permissionMenuMap()` | Peta `[permission_name => menu_id]` |
| `canDo($user, $menuKey, $action)` | Cek hak akses user pada menu+aksi |
| `canViewMenu($user, $menu)` | Apakah menu boleh tampil di sidebar |
| `sidebarFor($user)` | Pohon menu terfilter untuk sidebar |
| `permissionsOfRole($role)` | Daftar permission milik role |
| `syncRolePermissions($role, $names)` | Sinkronkan permission role (proteksi privilege escalation) |
| `copyPermissions($source, $target)` | Salin permission antar role |
| `resetPermissions($role)` | Kosongkan semua permission role |
| `rebuild()` | Buat ulang permission Spatie dari definisi menu |
| `clearCache()` | Bersihkan cache permission & menu |

### Caching

- Pohon menu dan peta permission di-cache dengan `Cache::rememberForever()`.
- Cache otomatis dihapus saat ada perubahan (sync/copy/reset/rebuild).
- Cache Spatie juga di-clear via `PermissionRegistrar::forgetCachedPermissions()`.

---

## 7. Controller

### `MenuPermissionController` (`app/Http/Controllers/MenuPermissionController.php`)

| Route | Method | Keterangan |
|---|---|---|
| `GET /system/permissions` | `index` | Matriks pohon menu × aksi per role |
| `PUT /system/permissions/{role}` | `update` | Simpan perubahan permission |
| `POST /system/permissions/{role}/copy` | `copy` | Copy permission dari role lain |
| `POST /system/permissions/{role}/reset` | `reset` | Reset (kosongkan) permission |
| `POST /system/permissions/cache/rebuild` | `rebuild` | Rebuild Permission |
| `POST /system/permissions/cache/clear` | `clearCache` | Clear Permission Cache |
| `GET /system/permissions/audit` | `audit` | Audit Log perubahan permission |

Semua route dilindungi middleware `auth` + `role:super_admin` dan policy check internal.

---

## 8. Middleware

### `CheckMenuPermission` (`app/Http/Middleware/CheckMenuPermission.php`)

Middleware RBAC berbasis permission. **Tidak menggunakan `if(role == admin)`** — murni cek permission via Gate.

```php
// Penggunaan pada route:
Route::get('/buku', ...)->middleware('menu:koleksi.buku.view');
Route::post('/buku', ...)->middleware('menu:koleksi.buku.create');
```

Jika user tidak memiliki hak akses → **403 Forbidden** dengan pesan:
> "Anda tidak memiliki hak akses."

Alias middleware didaftarkan di `bootstrap/app.php`:
```php
'menu' => \App\Http\Middleware\CheckMenuPermission::class,
```

---

## 9. Policy & Gate

### Policy

| Policy | Model | File |
|---|---|---|
| `MenuPermissionPolicy` | `Menu` | `app/Policies/MenuPermissionPolicy.php` |
| `ThemePolicy` | `ThemeSetting` | `app/Policies/ThemePolicy.php` |

### Gate::before (Super Admin Bypass)

```php
// app/Providers/AppServiceProvider.php
Gate::before(function ($user, $ability) {
    return $user->hasRole('super_admin') ? true : null;
});
```

### Provider Registration

```php
// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
];
```

---

## 10. Helper Functions

Didefinisikan di `app/Support/helpers.php`:

| Helper | Contoh | Keterangan |
|---|---|---|
| `user_can($perm)` | `user_can('koleksi.buku.create')` | Cek permission user login |
| `menu_can($key, $action)` | `menu_can('koleksi.buku', 'edit')` | Cek permission menu+aksi |
| `sidebar_menu()` | `sidebar_menu()` | Pohon menu terfilter |
| `permission_service()` | `permission_service()->tree()` | Akses PermissionService |

### Penggunaan di Blade

```blade
@if(user_can('koleksi.buku.create'))
    <button class="btn btn-primary">Tambah Buku</button>
@endif

@if(menu_can('sirkulasi.peminjaman', 'approve'))
    <button class="btn btn-success">Approve</button>
@endif
```

---

## 11. Blade Views

| View | Keterangan |
|---|---|
| `system/permissions/index.blade.php` | Matriks pohon menu dengan checkbox per aksi |
| `system/permissions/partials/menu-node.blade.php` | Partial rekursif node menu (tree) |
| `system/permissions/audit.blade.php` | Tabel audit log perubahan permission |
| `components/sidebar.blade.php` | Sidebar dinamis membaca permission |
| `errors/403.blade.php` | Halaman 403 Forbidden |

### UI Fitur

- **Tree Menu** — Menampilkan menu beserta submenu dalam bentuk pohon indentasi.
- **Checkbox** — Setiap aksi (View, Create, Edit, Delete, Export, Import, Print, dll) ditampilkan sebagai checkbox.
- **Select All / Unselect All** — Tombol untuk memilih/membatalkan semua checkbox.
- **Copy Permission** — Salin permission dari role lain.
- **Reset Permission** — Kosongkan semua permission role.
- **Rebuild Permission** — Bangun ulang permission dari definisi menu.
- **Clear Permission Cache** — Bersihkan cache permission.
- **Auto-check View** — Mencentang aksi selain View otomatis mencentang View (prasyarat).

---

## 12. Sidebar Dinamis

Sidebar otomatis membaca permission dari database:

```blade
@php
    $dynamicMenu = auth()->check() ? sidebar_menu() : [];
@endphp
```

- Jika menu sudah di-seed → sidebar dinamis (mengikuti permission).
- Jika menu belum di-seed → fallback ke sidebar statis (fitur lama tetap berfungsi).
- Menu tanpa hak akses **tidak ditampilkan**.

---

## 13. Dashboard Widget

Widget mengikuti permission:

```blade
@if(user_can('dashboard.view'))
    {{-- Tampilkan widget statistik --}}
@endif
```

Jika role tidak memiliki `dashboard.view`, widget tidak muncul.

---

## 14. Seeder

### `MenuPermissionSeeder` (`database/seeders/MenuPermissionSeeder.php`)

Dijalankan setelah `RolePermissionSeeder`. Proses:

1. Membuat 7 role RBAC (jika belum ada).
2. Membangun pohon menu sesuai struktur spesifikasi.
3. Mendaftarkan aksi/hak akses per menu.
4. Menjalankan `rebuild()` untuk membuat permission Spatie.
5. Memberikan default permission per role.

**Idempotent** — memakai `firstOrCreate`/`updateOrCreate` sehingga aman diulang.

### Default Permission per Role

| Role | Contoh Akses |
|---|---|
| Kepala Sekolah | Dashboard ✔, Laporan ✔, Literasi ✔ |
| Kepala Perpustakaan | Koleksi ✔, Sirkulasi ✔, Laporan ✔, Setting ✔ |
| Pustakawan | Koleksi ✔, Sirkulasi ✔, Laporan (view) ✔ |
| Guru | Dashboard ✔, Koleksi (view) ✔, Literasi ✔ |
| Siswa | Dashboard ✔, Koleksi (view) ✔, Literasi ✔ |
| Guest | Landing ✔ |

---

## 15. Audit Log

Setiap perubahan permission dicatat di tabel `audit_logs`:

| Field | Keterangan |
|---|---|
| `user_id` | Siapa yang mengubah |
| `action` | `update_menu_permission`, `copy_menu_permission`, `reset_menu_permission`, `rebuild_permission`, `clear_permission_cache` |
| `model_type` | Target (role:nama_role) |
| `before_data` | Permission lama (JSON) |
| `after_data` | Permission baru (JSON) |
| `created_at` | Kapan |

---

## 16. Security

✅ Semua pengecekan menggunakan **Policy** atau **Gate** — tidak ada `if(role == admin)`.  
✅ Permission-based, bukan role-based hardcode.  
✅ `syncRolePermissions()` memfilter hanya permission yang terdaftar pada peta menu (mencegah **privilege escalation**).  
✅ Super Admin bypass via `Gate::before` (bukan hardcode di controller).  
✅ Middleware `menu:` melindungi route.  
✅ Halaman 403 Forbidden untuk akses tidak sah.

---

## 17. Performance

✅ Permission di-cache dengan `Cache::rememberForever()`.  
✅ Eager loading pada relasi menu (`childrenRecursive`, `menuPermissions`).  
✅ Cache Spatie otomatis di-clear saat ada perubahan.  
✅ Pohon menu di-cache (tidak query DB setiap request).

---

## 18. Testing

### Feature Test: `tests/Feature/MenuPermissionTest.php`

| Test | Keterangan |
|---|---|
| `super_admin_can_open_menu_permission_page` | Super Admin bisa buka halaman permission |
| `non_super_admin_is_denied_from_menu_permission_page` | Non-admin ditolak (403) |
| `sidebar_follows_permission` | Sidebar mengikuti permission role |
| `menu_and_button_follow_permission` | Tombol CRUD mengikuti permission |
| `route_is_denied_when_role_lacks_permission` | Route ditolak jika role tidak punya akses |
| `super_admin_bypasses_permission_middleware` | Super Admin bypass middleware |
| `super_admin_can_update_role_permissions_and_audit_is_recorded` | Update permission + audit log |
| `super_admin_can_copy_and_reset_permissions` | Copy & reset permission |
| `super_admin_can_rebuild_and_clear_permission_cache` | Rebuild & clear cache |
| `widget_visibility_follows_permission` | Widget mengikuti permission |

### Unit Test: `tests/Unit/PermissionServiceTest.php`

| Test | Keterangan |
|---|---|
| `actions_and_roles_are_defined` | Definisi aksi & role tersedia |
| `tree_is_built_and_cached_from_database` | Tree menu dibangun dari DB |
| `rebuild_creates_spatie_permissions_from_menu_definitions` | Rebuild membuat permission Spatie |
| `sync_role_permissions_ignores_unknown_permissions` | Privilege escalation dicegah |
| `copy_and_reset_permissions_work` | Copy & reset berfungsi |

### Menjalankan Test

```bash
# Semua test RBAC
php artisan test --filter "MenuPermissionTest|PermissionServiceTest"

# Seluruh test suite
php artisan test
```

---

## 19. Menjalankan Seeder

```bash
# Seed lengkap (termasuk modul RBAC)
php artisan db:seed

# Seed hanya modul RBAC
php artisan db:seed --class=MenuPermissionSeeder
```

---

## 20. Daftar File

| File | Keterangan |
|---|---|
| `bootstrap/providers.php` | Registrasi AppServiceProvider |
| `bootstrap/app.php` | Middleware alias `menu` |
| `app/Providers/AppServiceProvider.php` | Gate::before, Policy registration |
| `database/migrations/2026_07_12_000002_create_menu_permission_tables.php` | Migration |
| `database/seeders/MenuPermissionSeeder.php` | Seeder |
| `app/Models/Menu.php` | Model Menu |
| `app/Models/MenuPermission.php` | Model MenuPermission |
| `app/Services/PermissionService.php` | Service RBAC |
| `app/Http/Controllers/MenuPermissionController.php` | Controller |
| `app/Http/Middleware/CheckMenuPermission.php` | Middleware |
| `app/Policies/MenuPermissionPolicy.php` | Policy |
| `app/Support/helpers.php` | Helper functions |
| `routes/modules/system.php` | Routes |
| `resources/views/system/permissions/index.blade.php` | View matriks |
| `resources/views/system/permissions/partials/menu-node.blade.php` | Partial tree node |
| `resources/views/system/permissions/audit.blade.php` | View audit log |
| `resources/views/components/sidebar.blade.php` | Sidebar dinamis |
| `resources/views/errors/403.blade.php` | Halaman 403 |
| `tests/Feature/MenuPermissionTest.php` | Feature test |
| `tests/Unit/PermissionServiceTest.php` | Unit test |
| `docs/MENU_PERMISSION.md` | Dokumentasi ini |
