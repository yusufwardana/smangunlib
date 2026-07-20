# SMANGUNLIB

SMANGUNLIB adalah aplikasi perpustakaan sekolah berbasis Laravel untuk katalog buku, anggota, sirkulasi, dokumen administrasi, literasi, laporan, landing page, backup, lisensi, dan pengaturan sistem.

> **Theme Manager** (System Settings → Theme Manager) memungkinkan administrator mengubah tampilan aplikasi & landing page tanpa mengubah kode. Lihat dokumentasi lengkap di [`docs/THEME_MANAGER.md`](docs/THEME_MANAGER.md).


## Kebutuhan Server

- Apache shared hosting cPanel.
- PHP 8.3 atau lebih baru.
- MySQL 8 atau kompatibel.
- Extension PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PDO MySQL, Tokenizer, XML, ZIP, GD, Intl.
- Document root diarahkan ke folder `public`.

## Checklist Sebelum Upload

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` sudah domain final.
- Jalankan build dan install dependency di lokal bila hosting tidak punya SSH.
- Jangan upload `.env` development, `node_modules`, log, session, cache lokal, atau database sqlite.
- Pastikan ZIP deployment berisi `vendor` bila hosting tidak menyediakan Composer/SSH.
- Pastikan `bootstrap/cache` tidak membawa cache dari komputer lokal.

## Panduan Deploy cPanel Tanpa SSH

1. Buat database MySQL dan user di cPanel, lalu beri semua privilege ke database aplikasi.
2. Upload ZIP proyek ke folder di luar `public_html` bila memungkinkan, misalnya `smangunlib`.
3. Extract ZIP.
4. Arahkan domain/subdomain document root ke `smangunlib/public`. Jika cPanel tidak mengizinkan, salin isi folder `public` ke `public_html` lalu sesuaikan path `../vendor/autoload.php` dan `../bootstrap/app.php` di `public_html/index.php`.
5. Copy `.env.example` menjadi `.env`.
6. Isi `APP_URL`, `APP_KEY`, koneksi database, mail, dan opsi session/cache.
7. Generate `APP_KEY` di lokal dengan `php artisan key:generate --show`, lalu paste hasilnya ke `.env` jika hosting tidak punya SSH.
8. Import SQL hasil migrasi/seeder melalui phpMyAdmin. Alternatifnya gunakan installer web `/install` bila database masih kosong.
9. Buat storage link. Jika symlink tidak tersedia, buat folder `public/storage` dan salin isi `storage/app/public` ke sana, atau gunakan fitur installer storage link.
10. Pastikan permission folder writable: `storage`, `storage/framework`, `storage/logs`, dan `bootstrap/cache`.
11. Login dengan akun admin yang dibuat saat installer/seeder, lalu segera ganti password default.

## Checklist Setelah Upload

- Halaman `/install` atau `/login` bisa dibuka.
- `storage/logs` writable dan tidak berisi error permission.
- Upload logo/gambar/dokumen bisa preview dan download.
- Landing page mengambil data dari menu, settings, dan content database.
- Route dashboard, koleksi, anggota, sirkulasi, dokumen, GLS, laporan, system settings dapat dibuka sesuai role.

## Troubleshooting

- 500 Internal Server Error: cek `storage/logs/laravel.log`, matikan debug di production setelah investigasi, pastikan PHP extension lengkap dan permission writable.
- 403 Forbidden: document root harus ke `public`, `.htaccess` harus ikut terupload, dan permission folder tidak terlalu ketat.
- 404 Not Found: aktifkan Apache rewrite, pastikan `.htaccess` ada di folder public.
- APP_KEY Missing: isi `APP_KEY` di `.env`.
- Storage Error: pastikan `storage/app/public`, `storage/framework`, dan `public/storage` tersedia.
- Permission Denied: set writable untuk `storage` dan `bootstrap/cache` melalui File Manager cPanel.
- Database Error: cek host, database, user, password, privilege, dan import SQL.
- Session/CSRF Error: pastikan `SESSION_DRIVER=file`, `storage/framework/sessions` writable, domain cookie sesuai.
- Route/Cache Error: hapus file cache di `bootstrap/cache/*.php`, lalu reload.
- Composer/Autoload Error: upload folder `vendor` yang sesuai `composer.lock`, atau jalankan Composer di lokal lalu upload ulang.

## Audit Production

Status saat audit: belum siap production sampai seluruh item critical/high di laporan audit ditutup, dependency Laravel 12 terkunci, secret tidak masuk Git, dan deployment cPanel diuji dari ZIP bersih.
