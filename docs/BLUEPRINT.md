# SMANGUNLIB — Blueprint & Dokumentasi Teknis

> Sistem Manajemen Perpustakaan Sekolah Menengah Atas
> Framework: **Laravel 12** · PHP **8.3+** · MySQL

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Tech Stack & Dependencies](#2-tech-stack--dependencies)
3. [Arsitektur Aplikasi](#3-arsitektur-aplikasi)
4. [Struktur Direktori](#4-struktur-direktori)
5. [Database Schema](#5-database-schema)
6. [Models & Relasi](#6-models--relasi)
7. [Roles & Permission (RBAC)](#7-roles--permission-rbac)
8. [Routes](#8-routes)
9. [Controllers](#9-controllers)
10. [Middleware](#10-middleware)
11. [Services](#11-services)
12. [Observers](#12-observers)
13. [Policies](#13-policies)
14. [Form Requests](#14-form-requests)
15. [Export Classes](#15-export-classes)
16. [Fitur Modul](#16-fitur-modul)
17. [Konfigurasi & Environment](#17-konfigurasi--environment)
18. [Proses Instalasi](#18-proses-instalasi)
19. [Security Notes & Audit](#19-security-notes--audit)
20. [Pengembangan & Kontribusi](#20-pengembangan--kontribusi)

---

## 1. Gambaran Umum

**SMANGUNLIB** adalah sistem manajemen perpustakaan berbasis web untuk sekolah menengah atas, mencakup:

| Fitur | Deskripsi |
|-------|-----------|
| **OPAC** | Online Public Access Catalogue untuk pencarian buku publik |
| **Katalog Buku** | Manajemen buku, eksemplar, rak lokasi, kategori |
| **Sirkulasi** | Peminjaman, pengembalian, perpanjangan, denda |
| **Anggota** | Manajemen anggota (siswa, guru, tendik) |
| **GLS** | Gerakan Literasi Sekolah — program literasi, log bacaan, peserta |
| **Dokumen** | Manajemen dokumen administrasi perpustakaan |
| **Inventaris** | Inventaris aset non-buku |
| **Buku Tamu** | Pencatatan kunjungan perpustakaan |
| **Laporan** | Export laporan ke Excel |
| **Sistem** | Backup, update, lisensi, tema, landing page, RBAC menu |
| **Installer** | Wizard instalasi pertama kali |

---

## 2. Tech Stack & Dependencies

### Backend

| Package | Versi | Fungsi |
|---------|-------|--------|
| `laravel/framework` | ^12.0 | Core framework |
| `spatie/laravel-permission` | ^6.0 | RBAC role & permission |
| `yajra/laravel-datatables-oracle` | ^11.0 | Server-side DataTables |
| `maatwebsite/excel` | ^3.1 | Export Excel/CSV |
| `intervention/image-laravel` | ^1.0 | Manipulasi gambar |

### Frontend

| Asset | Fungsi |
|-------|--------|
| Bootstrap 5.3 | UI framework |
| Bootstrap Icons | Ikon |
| Font Awesome 6 | Ikon tambahan |
| DataTables.js | Tabel interaktif |
| Vite | Asset bundler |

### Infrastruktur

- **PHP**: 8.3+
- **MySQL**: 8.0+ (FullText Index diperlukan untuk OPAC search)
- **PHP Extensions**: `zip`, `gd`, `pdo_mysql`, `mbstring`, `json`, `curl`

---

## 3. Arsitektur Aplikasi

```
┌─────────────────────────────────────────────────────────────┐
│                      HTTP Request                           │
└────────────────────────┬────────────────────────────────────┘
                         │
              ┌──────────▼──────────┐
              │    Web Middleware    │
              │  (CSRF, Session,    │
              │  CheckIfInstalled,  │
              │  auth, role)        │
              └──────────┬──────────┘
                         │
         ┌───────────────┼─────────────────┐
         │               │                 │
    ┌────▼────┐   ┌──────▼─────┐   ┌───── ▼─────┐
    │  Auth   │   │  Dashboard │   │   Modules  │
    │ Routes  │   │  Routes    │   │   Routes   │
    └────┬────┘   └──────┬─────┘   └─────┬──────┘
         │               │               │
         └───────────────▼───────────────┘
                         │
              ┌──────────▼──────────┐
              │     Controllers     │
              │  (Form Requests &   │
              │    Policies)        │
              └──────────┬──────────┘
                         │
              ┌──────────▼──────────┐
              │      Services       │
              │  (Business Logic)   │
              └──────────┬──────────┘
                         │
              ┌──────────▼──────────┐
              │  Models / Eloquent  │
              │  (Observers,        │
              │   SoftDeletes)      │
              └──────────┬──────────┘
                         │
              ┌──────────▼──────────┐
              │       MySQL DB      │
              └─────────────────────┘
```

### Flow Peminjaman

```
Pustakawan                SirkulasiController           DB
    │                             │                     │
    │── POST /sirkulasi/peminjaman ──►                  │
    │                             │── cek anggota      │
    │                             │── cek limit        │
    │                             │── cek denda tunggak│
    │                             │── lockForUpdate    │
    │                             │── Peminjaman::create─►
    │                             │── DetailPeminjaman─►
    │                             │── Eksemplar update─►
    │                             │── DB::commit()     │
    │◄── redirect cetak struk ───│                     │
```

---

## 4. Struktur Direktori

```
smangunlib/
├── app/
│   ├── Exports/               # Excel export classes
│   ├── Http/
│   │   ├── Controllers/       # 25+ controllers
│   │   ├── Middleware/        # 3 custom middleware
│   │   └── Requests/          # Form Request validations
│   ├── Models/                # 28 Eloquent models
│   ├── Observers/             # Model observers (audit log)
│   ├── Policies/              # Authorization policies
│   ├── Providers/             # AppServiceProvider, AuthServiceProvider
│   ├── Services/              # 8 service classes
│   └── Support/               # Helper functions
├── bootstrap/
│   ├── app.php                # Laravel bootstrap + middleware binding
│   └── providers.php
├── config/                    # Laravel config files
├── database/
│   ├── migrations/            # 33 migration files
│   └── seeders/               # DatabaseSeeder, RoleSeeder, dll
├── docs/                      # Dokumentasi (file ini)
├── public/                    # Web root
├── resources/
│   ├── views/                 # Blade templates (modul-based)
│   └── js/                    # Asset JS
├── routes/
│   ├── web.php                # Entry route loader
│   └── modules/               # Route files per modul
│       ├── auth.php
│       ├── opac.php
│       ├── buku.php
│       ├── anggota.php
│       ├── sirkulasi.php
│       ├── katalog.php
│       ├── gls.php
│       ├── dokumen.php
│       ├── inventaris.php
│       ├── buku_tamu.php
│       ├── laporan.php
│       ├── reservasi.php
│       └── system.php
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 5. Database Schema

### Tabel: `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `name` | varchar(255) | |
| `email` | varchar(255) unique | |
| `password` | varchar(255) | bcrypt |
| `remember_token` | varchar(100) | |
| `email_verified_at` | timestamp nullable | |
| `deleted_at` | timestamp nullable | SoftDelete |
| `created_at`, `updated_at` | timestamp | |

### Tabel: `anggota`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | FK → users nullable unique | Login account |
| `nomor_anggota` | varchar(50) unique | |
| `tipe_anggota` | enum: siswa, guru, tendik | |
| `no_identitas` | varchar(50) unique | NIS/NISN/NIP |
| `jenis_kelamin` | enum: L, P | |
| `tempat_lahir` | varchar(100) | |
| `tanggal_lahir` | date | |
| `alamat` | text | |
| `no_telepon` | varchar(20) nullable | |
| `foto` | varchar(255) nullable | *added via migration 021* |
| `status` | enum: aktif, non-aktif, blacklist | default: aktif |
| `masa_berlaku_sampai` | date | |
| `deleted_at` | timestamp nullable | SoftDelete |

### Tabel: `buku`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `isbn` | varchar(20) nullable unique | |
| `judul` | varchar(255) | FULLTEXT indexed |
| `pengarang` | varchar(255) | FULLTEXT indexed |
| `penerbit` | varchar(150) | |
| `tahun_terbit` | year | |
| `edisi` | varchar(50) nullable | |
| `halaman` | integer nullable | |
| `bahasa` | varchar(50) | |
| `deskripsi` | text nullable | |
| `cover_image` | varchar(255) nullable | |
| `rak_lokasi_id` | FK → rak_lokasi nullable | |
| `file_digital` | varchar(255) nullable | *migration 020* |
| `url_external` | varchar(255) nullable | *migration 020* |
| `tipe_digital` | enum: pdf, epub, link nullable | *migration 020* |
| `deleted_at` | timestamp nullable | SoftDelete |

### Tabel: `eksemplar`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `buku_id` | FK → buku | restrict on delete |
| `nomor_barcode` | varchar(50) unique | |
| `tanggal_pengadaan` | date | |
| `asal_pengadaan` | varchar(100) | |
| `harga` | decimal(12,2) nullable | |
| `kondisi` | enum: baik, rusak_ringan, rusak_berat, hilang | |
| `status_sirkulasi` | enum: tersedia, dipinjam, weeding | |
| `deleted_at` | timestamp nullable | SoftDelete |

### Tabel: `kategori`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama` | varchar(100) unique | |
| `kode_ddc` | varchar(20) nullable | Dewey Decimal Classification |
| `deskripsi` | text nullable | |

### Tabel: `buku_kategori` (pivot)
| Kolom | Tipe |
|-------|------|
| `buku_id` | FK → buku |
| `kategori_id` | FK → kategori |

### Tabel: `rak_lokasi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama_rak` | varchar(100) unique | |
| `kode_rak` | varchar(20) unique | |
| `deskripsi` | text nullable | |

### Tabel: `peminjaman`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nomor_transaksi` | varchar(50) unique | Format: TRX-YYYYMMDD-XXXX |
| `anggota_id` | FK → anggota | |
| `user_id` | FK → users | Pustakawan yang melayani |
| `tanggal_pinjam` | date indexed | |
| `due_date` | date indexed | |
| `status` | enum: dipinjam, dikembalikan | |
| `perpanjangan_count` | tinyint default 0 | *migration 022* |
| `keterangan` | text nullable | |
| `deleted_at` | timestamp nullable | SoftDelete |

### Tabel: `detail_peminjaman`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `peminjaman_id` | FK → peminjaman | cascade delete |
| `eksemplar_id` | FK → eksemplar | restrict delete |
| `tanggal_kembali` | date nullable | |
| `kondisi_kembali` | enum: baik, rusak, hilang nullable | |
| `status` | enum: dipinjam, dikembalikan, hilang | |
| `deleted_at` | timestamp nullable | SoftDelete |

### Tabel: `denda`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `detail_peminjaman_id` | FK → detail_peminjaman unique | 1:1 |
| `anggota_id` | FK → anggota | |
| `jumlah_hari_terlambat` | integer | |
| `tarif_per_hari` | decimal(10,2) | Dari Setting::get('denda_per_hari') |
| `total_denda` | decimal(10,2) | |
| `status_pembayaran` | enum: belum_lunas, lunas, waived | |
| `tanggal_bayar` | datetime nullable | |
| `user_id` | FK → users nullable | Pustakawan penerima |
| `alasan_waive` | text nullable | |
| `deleted_at` | timestamp nullable | SoftDelete |

### Tabel: `reservasi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `anggota_id` | FK → anggota | |
| `buku_id` | FK → buku | |
| `status` | enum: pending, aktif, batal, selesai | |
| `expired_at` | timestamp nullable | |

### Tabel: `buku_tamu`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama` | varchar(255) | |
| `no_identitas` | varchar(50) nullable | |
| `tipe_pengunjung` | enum | siswa, guru, tendik, umum |
| `kelas_instansi` | varchar(100) nullable | |
| `keperluan` | text | |
| `tanggal_kunjungan` | date | |
| `jam_masuk` | time | |
| `jam_keluar` | time nullable | |

### Tabel: `inventaris`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama_barang` | varchar(255) | |
| `kode_barang` | varchar(50) unique | |
| `jumlah` | integer | |
| `kondisi` | enum: baik, rusak, hilang | |
| `tanggal_pengadaan` | date | |
| `keterangan` | text nullable | |

### Tabel: `program_literasi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | FK → users | Pembuat |
| `nama_program` | varchar(255) | |
| `deskripsi` | text nullable | |
| `tanggal_mulai` | date | |
| `tanggal_selesai` | date nullable | |
| `status` | enum: aktif, selesai, batal | |

### Tabel: `peserta_literasi` (pivot + data)
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `program_literasi_id` | FK → program_literasi | |
| `anggota_id` | FK → anggota | |
| `tanggal_daftar` | date | |
| `status_kehadiran` | enum: terdaftar, hadir, absen | |

### Tabel: `log_bacaan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `anggota_id` | FK → anggota | |
| `buku_id` | FK → buku nullable | |
| `judul_buku_manual` | varchar(255) nullable | Buku non-katalog |
| `tanggal_baca` | date | |
| `halaman_dibaca` | integer | |
| `keterangan` | text nullable | |

### Tabel: `dokumentasi_literasi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `program_literasi_id` | FK → program_literasi | |
| `user_id` | FK → users | |
| `judul` | varchar(255) | |
| `file_path` | varchar(255) | |
| `tipe` | enum: foto, video, dokumen | |

### Tabel: `dokumen_administrasi`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | FK → users | |
| `judul` | varchar(255) | |
| `kategori_dokumen` | varchar(100) | |
| `file_path` | varchar(255) | |
| `deskripsi` | text nullable | |
| `versi` | varchar(20) nullable | *migration 024* |
| `is_active` | boolean default true | *migration 024* |

### Tabel: `audit_logs`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `user_id` | FK → users nullable | |
| `event` | varchar(50) | created, updated, deleted |
| `auditable_type` | varchar(255) | Polymorphic model class |
| `auditable_id` | bigint | Polymorphic ID |
| `old_values` | json nullable | |
| `new_values` | json nullable | |
| `ip_address` | varchar(45) nullable | |
| `user_agent` | text nullable | |
| `created_at` | timestamp | |

### Tabel: `settings`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `key` | varchar(255) unique | |
| `value` | text nullable | |
| `group` | varchar(100) nullable | |
| `created_at`, `updated_at` | timestamp | |

**Kunci settings yang digunakan:**
- `maksimal_pinjam` — batas buku per peminjaman (default: 3)
- `lama_pinjam_default` — hari peminjaman (default: 7)
- `denda_per_hari` — tarif denda Rp/hari (default: 1000)
- `tahun_ajaran`, `semester`
- `smtp_*`, `whatsapp_*` — notifikasi
- `nama_sekolah`, `nama_perpustakaan`, `logo`

### Tabel: `backups`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `nama_file` | varchar(255) | |
| `tipe` | enum: database, storage, full | |
| `ukuran_mb` | decimal(10,2) | |
| `user_id` | FK → users | |
| `status` | enum: pending, completed, failed | |

### Tabel: `system_updates`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `versi` | varchar(20) | |
| `nama_file` | varchar(255) | |
| `catatan` | text nullable | |
| `user_id` | FK → users | |
| `status` | enum: uploaded, applied, failed | |

### Tabel: `licenses`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `license_key` | varchar(255) unique | |
| `status` | enum: active, expired, invalid | |
| `expired_at` | timestamp nullable | |
| `metadata` | json nullable | |

### Tabel: `theme_settings`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | bigint PK | |
| `key` | varchar(255) unique | |
| `value` | text nullable | |

### Tabel: `landing_contents` & `landing_menus`
Mengelola konten dan navigasi halaman landing publik.

### Tabel: `media_assets`
Manajemen file gambar/dokumen yang diupload melalui Media Manager.

### Tabel: Spatie Permission
- `roles` — master roles
- `permissions` — master permissions  
- `model_has_roles` — user ↔ role
- `model_has_permissions` — user ↔ permission
- `role_has_permissions` — role ↔ permission

### Tabel: `menus` & `menu_permissions`
Mendefinisikan pohon menu navigasi dan hak akses per role.

---

## 6. Models & Relasi

```
User
  └── hasOne → Anggota
  └── hasRoles (Spatie)

Anggota
  ├── belongsTo → User
  ├── hasMany → Peminjaman
  ├── hasMany → Reservasi
  ├── hasMany → Denda
  ├── hasMany → LogBacaan
  └── belongsToMany → ProgramLiterasi (via peserta_literasi)

Buku
  ├── hasMany → Eksemplar
  ├── hasMany → LogBacaan
  ├── hasMany → Reservasi
  ├── belongsToMany → Kategori (via buku_kategori)
  └── belongsTo → RakLokasi

Eksemplar
  ├── belongsTo → Buku
  └── hasMany → DetailPeminjaman

Peminjaman
  ├── belongsTo → Anggota
  ├── belongsTo → User (pustakawan)
  └── hasMany → DetailPeminjaman

DetailPeminjaman
  ├── belongsTo → Peminjaman
  ├── belongsTo → Eksemplar
  └── hasOne → Denda

Denda
  ├── belongsTo → DetailPeminjaman
  ├── belongsTo → Anggota
  └── belongsTo → User

ProgramLiterasi
  ├── belongsTo → User
  ├── hasMany → PesertaLiterasi
  └── hasMany → DokumentasiLiterasi

Setting (static helper)
  └── static get($key), static set($key, $value)
```

---

## 7. Roles & Permission (RBAC)

### Roles

| Role | Deskripsi |
|------|-----------|
| `super_admin` | Akses penuh semua modul + sistem |
| `kepala_perpustakaan` | Semua modul perpustakaan + setting |
| `pustakawan` | Sirkulasi, katalog, anggota, GLS, buku tamu |
| `anggota` | Portal anggota, OPAC, reservasi, log bacaan |

### RBAC Menu (MenuPermission)

Matriks hak akses per role × menu × aksi (`view`, `create`, `edit`, `delete`) disimpan di tabel `menu_permissions` dan di-cache. Rebuild cache dapat dilakukan via `POST /system/permissions/cache/rebuild`.

---

## 8. Routes

### Auth (`routes/modules/auth.php`)
| Method | URI | Handler | Middleware |
|--------|-----|---------|------------|
| GET | `/login` | `AuthController@showLoginForm` | guest |
| POST | `/login` | `AuthController@login` | guest |
| POST | `/logout` | `AuthController@logout` | auth |

### OPAC — Publik (`routes/modules/opac.php`)
| Method | URI | Handler |
|--------|-----|---------|
| GET | `/` | Landing page |
| GET | `/opac` | `OpacController@index` |
| GET | `/opac/{buku}` | `OpacController@show` |
| GET | `/opac/{buku}/read` | `OpacController@read` (E-Book) |

### Dashboard
| GET | `/dashboard` | `DashboardController@index` | auth, role:any |

### Buku (`routes/modules/buku.php`) — auth + role
| Method | URI | Handler |
|--------|-----|---------|
| GET | `/buku` | `BukuController@index` |
| GET | `/buku/create` | `BukuController@create` |
| POST | `/buku` | `BukuController@store` |
| GET | `/buku/{buku}` | `BukuController@show` |
| GET | `/buku/{buku}/edit` | `BukuController@edit` |
| PUT | `/buku/{buku}` | `BukuController@update` |
| DELETE | `/buku/{buku}` | `BukuController@destroy` |
| GET | `/buku/{buku}/eksemplar` | `EksemplarController@index` |
| POST | `/buku/{buku}/eksemplar` | `EksemplarController@store` |
| PUT/DELETE | `/buku/{buku}/eksemplar/{eksemplar}` | CRUD eksemplar |

### Anggota (`routes/modules/anggota.php`) — auth + role
Standard CRUD + import Excel + cetak kartu.

### Sirkulasi (`routes/modules/sirkulasi.php`) — auth + pustakawan+
| Method | URI | Handler |
|--------|-----|---------|
| GET | `/sirkulasi` | `SirkulasiController@index` |
| GET | `/sirkulasi/peminjaman` | `SirkulasiController@peminjamanForm` |
| POST | `/sirkulasi/peminjaman` | `SirkulasiController@storePeminjaman` |
| GET | `/sirkulasi/pengembalian/{nomor?}` | `SirkulasiController@pengembalianForm` |
| POST | `/sirkulasi/pengembalian/{id}` | `SirkulasiController@prosesPengembalian` |
| POST | `/sirkulasi/perpanjang/{id}` | `SirkulasiController@perpanjang` |
| GET | `/sirkulasi/cetak-struk/{id}` | `SirkulasiController@cetakStruk` |

### Sistem (`routes/modules/system.php`) — auth + super_admin
| Method | URI | Handler |
|--------|-----|---------|
| GET/POST | `/system/settings` | SystemSettingsController |
| GET/POST | `/system/backup` | BackupController |
| GET | `/system/backup/download/{id}` | BackupController@download |
| DELETE | `/system/backup/{id}` | BackupController@destroy |
| GET/POST | `/system/update` | SystemUpdateController |
| GET/POST | `/system/license` | LicenseController |
| GET | `/system/info` | SystemInfoController@index |
| GET/PUT | `/system/permissions` | MenuPermissionController |
| POST | `/system/permissions/cache/rebuild` | MenuPermissionController@rebuild |
| GET/POST | `/system/theme` | ThemeController |
| GET/POST | `/system/contents` | LandingContentController |
| GET/POST | `/system/menus` | LandingMenuController |
| GET/POST | `/system/media` | MediaManagerController |

### Installer (`/install/*`) — RedirectIfInstalled
Wizard multi-langkah: welcome → requirements → permissions → database → app config → process → admin → config → finish.

---

## 9. Controllers

| Controller | Tanggung Jawab |
|-----------|----------------|
| `AuthController` | Login, logout, rate limiting |
| `DashboardController` | Stats overview dashboard |
| `BukuController` | CRUD buku + DataTables |
| `EksemplarController` | CRUD eksemplar per buku |
| `KategoriController` | CRUD kategori DDC |
| `RakLokasiController` | CRUD rak lokasi |
| `AnggotaController` | CRUD anggota + import + kartu |
| `SirkulasiController` | Peminjaman, pengembalian, perpanjangan, denda |
| `DendaController` | Manajemen denda, pembayaran, waive |
| `ReservasiController` | Reservasi buku |
| `BukuTamuController` | Pencatatan kunjungan |
| `InventarisController` | CRUD inventaris |
| `GlsController` | Program literasi CRUD |
| `LogBacaanController` | Log baca anggota |
| `DokumenController` | Dokumen administrasi |
| `OpacController` | Pencarian publik + detail buku |
| `LaporanController` | Generate & export laporan Excel |
| `SystemInfoController` | Info server, PHP, DB |
| `SystemSettingsController` | Pengaturan aplikasi |
| `BackupController` | Backup DB/storage |
| `SystemUpdateController` | Upload & apply update |
| `LicenseController` | Manajemen lisensi |
| `ThemeController` | Tema landing & dashboard |
| `LandingContentController` | Konten halaman publik |
| `LandingMenuController` | Menu navigasi publik |
| `MediaManagerController` | Manajemen media asset |
| `MenuPermissionController` | RBAC menu × role |
| `InstallerController` | Proses instalasi wizard |

---

## 10. Middleware

| Middleware | Class | Fungsi |
|-----------|-------|--------|
| `check.installed` | `CheckIfInstalled` | Redirect ke installer jika file marker tidak ada |
| `redirect.if.installed` | `RedirectIfInstalled` | **abort(403)** jika marker ADA + cek user DB |
| `role` | Spatie `RoleMiddleware` | Validasi role user vs route yang dilindungi |
| `auth` | Laravel built-in | Cek autentikasi session |
| `guest` | Laravel built-in | Redirect jika sudah login |

### Urutan Middleware Stack (bootstrap/app.php)
```
web → check.installed → [route-specific: auth, role:xxx]
```

---

## 11. Services

| Service | Tanggung Jawab |
|---------|---------------|
| `InstallerService` | Cek requirements, permissions, test DB, tulis .env, buat symlink |
| `BackupService` | Backup database (mysqldump) dan storage ke ZIP |
| `SystemUpdateService` | Validasi ZIP update (checksum SHA-256 + path whitelist + allowedRoots), apply update |
| `LicenseService` | Validasi & aktivasi lisensi |
| `ThemeService` | Get/set theme settings, export/import theme JSON |
| `PermissionService` | Rebuild menu permission cache, copy permission antar role |
| `SystemInfoService` | Info PHP, extensions, disk, DB version |
| `ActivityLogger` | Logging aktivitas user (wrapper AuditLog) |

### SystemUpdateService — Detail Validasi ZIP
```
validateAndExtract($zipPath)
  ├── validateZipEntries() → cek path traversal (../) + allowed roots only
  │     allowed: app/, bootstrap/, config/, database/, public/, resources/, routes/
  ├── cek manifest.json ada di root ZIP
  ├── extract ke temp dir
  ├── parse manifest.json (wajib: version, checksum)
  └── hashExtractedPayload() → SHA-256 semua file (sorted, deterministic)
       └── hash_equals(manifest.checksum, computed_hash)
```

---

## 12. Observers

| Observer | Model | Events | Aksi |
|----------|-------|--------|------|
| `BukuObserver` | Buku | created, updated, deleted | AuditLog |
| `AnggotaObserver` | Anggota | created, updated, deleted | AuditLog |
| `PeminjamanObserver` | Peminjaman | created, updated | AuditLog |

---

## 13. Policies

| Policy | Model | Aksi yang Dilindungi |
|--------|-------|----------------------|
| `BukuPolicy` | Buku | create, update, delete |
| `AnggotaPolicy` | Anggota | create, update, delete |
| `PeminjamanPolicy` | Peminjaman | create, prosesPengembalian |
| `DendaPolicy` | Denda | update (bayar/waive) |
| `DokumenPolicy` | DokumenAdministrasi | create, update, delete |

---

## 14. Form Requests

| Request | Controller Method | Aturan Utama |
|---------|-----------------|--------------|
| `LoginRequest` | `AuthController@login` | email required, password required, remember nullable boolean |
| `StorePeminjamanRequest` | `SirkulasiController@storePeminjaman` | anggota_id, eksemplar_ids array |
| `ProsesPengembalianRequest` | `SirkulasiController@prosesPengembalian` | detail.*.kondisi_kembali |
| `StoreBukuRequest` | `BukuController@store` | judul, pengarang, penerbit wajib |
| `StoreAnggotaRequest` | `AnggotaController@store` | validasi unique no_identitas |
| `ProcessBackupRequest` | `BackupController@process` | tipe enum: database, storage, full |

---

## 15. Export Classes (`app/Exports/`)

| Class | Laporan | Format |
|-------|---------|--------|
| `PeminjamanExport` | Riwayat peminjaman | Excel |
| `AnggotaExport` | Data anggota | Excel |
| `DendaExport` | Laporan denda | Excel |
| `StatistikExport` | Statistik kunjungan & sirkulasi | Excel |

---

## 16. Fitur Modul

### OPAC (Online Public Access Catalogue)
- Full-text search MySQL pada `judul` + `pengarang`
- Filter: kategori, tahun, bahasa, ketersediaan
- Detail buku: cover, deskripsi, eksemplar tersedia
- Preview/baca E-Book (PDF, ePub, external link)
- Publik — tidak perlu login

### Sirkulasi
- Peminjaman multi-eksemplar dalam satu transaksi
- Limit buku dari `Setting::get('maksimal_pinjam')` (default: 3)
- Durasi pinjam dari `Setting::get('lama_pinjam_default')` (default: 7 hari)
- Cek denda tertunggak sebelum izinkan pinjam baru
- Pengembalian parsial (per buku)
- Perpanjangan 1x selama belum terlambat (+3 hari)
- Denda otomatis dari `Setting::get('denda_per_hari')` (default: Rp 1.000/hari)
- Denda tambahan jika buku hilang (harga buku)
- Cetak struk peminjaman/pengembalian
- `lockForUpdate()` pada perpanjangan untuk mencegah race condition

### GLS (Gerakan Literasi Sekolah)
- Program literasi: nama, deskripsi, periode, status
- Pendaftaran peserta anggota
- Presensi kehadiran
- Log bacaan anggota (buku katalog atau manual)
- Dokumentasi foto/video/dokumen per program

### Buku Tamu
- Catat kunjungan: nama, identitas, keperluan, jam masuk/keluar
- Laporan statistik kunjungan

### Sistem
- **Backup**: database (mysqldump) + storage ZIP, download, delete
- **Update**: upload ZIP update, validasi checksum SHA-256, apply ke core
- **Lisensi**: aktivasi & pengecekan expired
- **Tema**: kustomisasi warna, font, logo, background login
- **Landing**: editor konten halaman publik + menu navigasi
- **Media Manager**: upload & kelola file gambar/dokumen
- **RBAC Menu**: matriks akses menu × role × aksi dengan cache

---

## 17. Konfigurasi & Environment

### `.env` Kunci Penting

```env
APP_NAME=SMANGUNLIB
APP_ENV=production
APP_KEY=            # Auto-generated saat install
APP_URL=https://domain-perpustakaan.sch.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smangunlib
DB_USERNAME=db_user
DB_PASSWORD=db_pass

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@sekolah.sch.id
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
```

### Setting Aplikasi (tabel `settings`)

Dikelola via `/system/settings`. Diakses di kode via `Setting::get('key')` dan `Setting::set('key', 'value')`.

---

## 18. Proses Instalasi

Wizard installer tersedia di `/install/welcome`. Dilindungi oleh `RedirectIfInstalled` middleware yang memblokir akses jika:
1. File `storage/app/installed` sudah ada, **ATAU**
2. Tabel `users` sudah berisi data

**Langkah-langkah:**

```
1. /install/welcome      → Selamat datang
2. /install/requirements → Cek PHP extensions
3. /install/permissions  → Cek folder writable
4. /install/database     → Test koneksi DB (AJAX)
5. /install/app          → Nama & URL aplikasi
6. /install/process      → [AJAX multi-step]
     ├── processEnv()    → Generate .env
     ├── processKey()    → php artisan key:generate
     ├── processSymlink()→ php artisan storage:link
     ├── processMigrate()→ php artisan migrate --force
     └── processSeed()   → php artisan db:seed --force
7. /install/admin        → Buat akun super_admin
8. /install/config       → Setting awal (denda, limit, dll)
9. /install/finish       → Buat file storage/app/installed
```

---

## 19. Security Notes & Audit

### Perbaikan yang Telah Diimplementasikan

| Kategori | Deskripsi | File | Status |
|----------|-----------|------|--------|
| **Installer Protection** | Double-layer block: file marker + DB user check; abort(403) bukan redirect | `RedirectIfInstalled.php` | ✅ Fixed |
| **Password in Session** | Dihapus penyimpanan plaintext password di session installer | `InstallerController.php` | ✅ Fixed |
| **ZIP Update — RCE** | Validasi: checksum SHA-256, path traversal block, allowed roots whitelist | `SystemUpdateService.php` | ✅ Already Secure |
| **Backup Path Traversal** | `basename()` sanitization pada nama file sebelum download/delete | `BackupController.php` | ✅ Fixed |
| **Race Condition Perpanjang** | `lockForUpdate()` pada query peminjaman | `SirkulasiController.php` | ✅ Fixed |
| **Hardcoded Business Rules** | Limit pinjam, lama pinjam, denda diambil dari `Setting::get()` | `SirkulasiController.php` | ✅ Fixed |
| **Login Checkbox Validation** | Tambah `value="1"` pada checkbox remember — sebelumnya mengirim `"on"` yang gagal rule `boolean` | `login.blade.php` | ✅ Fixed |

### Best Practices yang Sudah Ada

- **Rate Limiting** login: 5 percobaan / 60 detik per email+IP
- **Session Regeneration** setelah login berhasil
- **CSRF Protection** aktif pada semua form
- **SoftDeletes** pada semua model utama (data tidak hilang permanen)
- **DB Transaction** pada operasi sirkulasi
- **Form Request Validation** — validasi tidak dilakukan di controller
- **Policy Authorization** — keputusan otorisasi terpisah dari business logic
- **Observer Pattern** — audit log otomatis tanpa polusi controller

### Rekomendasi Lanjutan

- [ ] Tambah HTTPS enforcement di production (`APP_FORCE_HTTPS=true`)
- [ ] Aktifkan `email_verified_at` untuk verifikasi email anggota
- [ ] Rate limit juga untuk endpoint OPAC pencarian (cegah scraping)
- [ ] Job/Queue untuk proses backup dan update agar tidak timeout
- [ ] Unit test untuk `SirkulasiController` dan `SystemUpdateService`

---

## 20. Pengembangan & Kontribusi

### Setup Development

```bash
git clone https://github.com/yusufwardana/smangunlib.git
cd smangunlib
composer install
npm install
cp .env.example .env
# Konfigurasi .env (DB, APP_URL)
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

### Testing

```bash
php artisan test
php artisan test --filter SirkulasiTest
```

### Asset Build

```bash
# Development (dengan hot reload)
npm run dev

# Production
npm run build
```

### Konvensi Kode

- PSR-12 coding standard
- Snake_case untuk nama tabel dan kolom DB
- PascalCase untuk class PHP
- camelCase untuk method PHP
- Blade: kebab-case untuk component name

---

*Dokumen dihasilkan pada: 2026-07-20 | Versi SMANGUNLIB: lihat `CHANGELOG.md`*