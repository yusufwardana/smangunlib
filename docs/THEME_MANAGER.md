# Modul Theme Manager

Dokumentasi modul **Theme Manager** untuk Sistem Informasi Perpustakaan SMA
(Laravel 12 + Blade + Bootstrap 5 + Vite).

Modul ini memungkinkan administrator mengubah tampilan aplikasi dan landing page
(warna, logo, tipografi, layout, dsb.) **tanpa mengubah kode**. Seluruh pengaturan
disimpan di database dan dipetakan menjadi CSS Variables sehingga bisa dipakai ulang
di Bootstrap 5 dan komponen custom.

---

## 1. Arsitektur

```
Browser (Blade + theme-manager.js)
        │  AJAX (live preview / simpan)
        ▼
routes/modules/system.php  ──►  ThemeController
                                    │  (validasi via FormRequest)
                                    ▼
                                ThemeService  ──►  Cache  ──►  ThemeSetting (MySQL)
                                    │
                                    ▼
                          CSS Variables (:root { --primary-color: … })
```

- **ThemeSetting** — Model Eloquent untuk tabel `theme_settings`.
- **ThemeService** — satu-satunya titik akses baca/tulis tema. Menggunakan cache
  sehingga tidak ada query berulang ke database dalam satu request.
- **LoadTheme** middleware — men-share nilai tema & blok CSS variables ke seluruh view.
- **helper `theme()`** — pembungkus praktis untuk membaca nilai tema di Blade/PHP.

---

## 2. Database

Tabel: `theme_settings`

| Kolom        | Tipe        | Keterangan                                   |
|--------------|-------------|----------------------------------------------|
| `id`         | bigint (PK) | Primary key                                  |
| `group`      | string      | Kelompok pengaturan (`general`, `color`, …)  |
| `key`        | string      | Kunci pengaturan (`primary_color`, …)        |
| `value`      | text/null   | Nilai pengaturan                             |
| `type`       | string      | Tipe nilai (`string`, `color`, `boolean`, `image`, `select`, `range`, `textarea`) |
| `created_at` | timestamp   |                                              |
| `updated_at` | timestamp   |                                              |

Pasangan `group` + `key` bersifat unik.

---

## 3. Instalasi / Aktivasi

Migrasi & seeder sudah terdaftar. Untuk lingkungan baru:

```bash
php composer.phar dump-autoload   # memuat helper app/Support/helpers.php
php artisan migrate
php artisan db:seed --class=ThemeSettingSeeder
php artisan storage:link          # agar logo/favicon dapat diakses publik
npm run build                     # kompilasi resources/css/theme.css & JS
```

> Helper `theme()`, `theme_asset()`, dan `theme_css_variables()` didaftarkan lewat
> `composer.json` (`autoload.files`). Jalankan `dump-autoload` bila belum tersedia.

---

## 4. Grup Pengaturan

Modul menyediakan 20 area pengaturan:

1. **General** — nama tema, mode (Light/Dark/Auto), border radius, shadow, animasi, font family, font size.
2. **Color** — primary, secondary, success, danger, warning, info, background, sidebar, navbar, card, footer, text, link, hover, button.
3. **Logo** — logo sekolah, perpustakaan, login, sidebar, footer (upload + preview).
4. **Favicon** — upload favicon + preview.
5. **Login** — background, overlay, warna card, warna tombol, background image, video background.
6. **Dashboard** — sidebar, navbar, warna widget, warna chart, gaya tabel, gaya card.
7. **Landing Page** — hero background, gradient, gaya tombol, background section, footer, tipografi.
8. **Typography** — Google Fonts / System Fonts, heading font, body font, font weight.
9. **Button** — rounded/square/pill, shadow, outline/filled.
10. **Card** — flat, shadow, glassmorphism, bordered.
11. **Sidebar** — collapsed default, mini sidebar, lebar, posisi.
12. **Navbar** — sticky, transparent, solid, blur.
13. **Layout** — boxed/full width, container width.
14. **Loading Screen** — enable, gaya spinner, logo loading.
15. **Custom CSS** — textarea CSS tambahan.
16. **Custom JS** — textarea JavaScript tambahan.

Plus fitur global: **Live Preview**, **Reset**, **Export JSON**, **Import JSON**.

---

## 5. Helper `theme()`

```php
theme('primary_color');            // pencarian singkat lintas grup
theme('color.primary_color');      // bentuk penuh "group.key"
theme('logo.logo_sekolah');
theme('general.font_family', "'Inter', sans-serif"); // dengan default
theme();                           // seluruh map (array group.key => value)
```

Helper aset & CSS variables:

```php
theme_asset('logo.logo_sekolah');  // URL publik file yang di-upload
theme_css_variables();             // blok ":root { --primary-color: …; }"
```

---

## 6. Penggunaan di Blade

`LoadTheme` middleware sudah men-share variabel, sehingga cukup:

```blade
{{-- layouts/app.blade.php --}}
<style>
    {!! theme_css_variables() !!}
    {!! theme('custom.custom_css') !!}
</style>

<img src="{{ theme_asset('logo.logo_sekolah', asset('images/logo-default.png')) }}" alt="Logo">
```

CSS Variables yang tersedia (subset):

```css
--primary-color, --secondary-color, --success-color, --danger-color,
--warning-color, --info-color, --bg-color, --sidebar-color, --navbar-color,
--card-color, --footer-color, --text-color, --link-color, --hover-color,
--button-color, --font-family, --font-size, --border-radius, --sidebar-width
```

Gunakan di CSS/Bootstrap 5:

```css
.btn-primary { background-color: var(--primary-color); }
.sidebar     { background-color: var(--sidebar-color); width: var(--sidebar-width); }
body         { font-family: var(--font-family); font-size: var(--font-size); }
```

---

## 7. Rute

Terdaftar di `routes/modules/system.php`, dilindungi middleware
`auth` + `role:super_admin|kepala_perpustakaan`:

| Method | URI                     | Nama                    | Aksi                       |
|--------|-------------------------|-------------------------|----------------------------|
| GET    | `/system/theme`         | `system.theme.index`    | Tampilkan halaman          |
| POST   | `/system/theme`         | `system.theme.update`   | Simpan grup (AJAX/redirect)|
| POST   | `/system/theme/preview` | `system.theme.preview`  | Live preview (tanpa simpan)|
| POST   | `/system/theme/reset`   | `system.theme.reset`    | Reset ke default           |
| GET    | `/system/theme/export`  | `system.theme.export`   | Export JSON                |
| POST   | `/system/theme/import`  | `system.theme.import`   | Import JSON                |

---

## 8. Live Preview (AJAX)

`public/js/theme-manager.js` mengirim perubahan form ke endpoint dan menyuntikkan
blok CSS ke `<style id="theme-live-preview">` sehingga perubahan langsung terlihat
**tanpa reload**. Menyimpan akan mem-persist ke database dan menyegarkan cache.

---

## 9. Keamanan Upload

Divalidasi oleh `UpdateThemeRequest`:

- Ukuran maksimal **5 MB** per file.
- Format didukung: **PNG, JPG, SVG, WEBP, ICO** (dan MP4/WEBM untuk video background login).
- Aset lama otomatis dihapus saat diganti.
- Otorisasi: hanya `super_admin` & `kepala_perpustakaan`.

---

## 10. Cache & Performa

`ThemeService` menyimpan seluruh map tema dalam satu key cache (`theme.settings.map`).

- Baca: satu hit cache, **tanpa query berulang**.
- Tulis / reset / import: cache otomatis di-*flush* lalu dibangun ulang.

---

## 11. Reset / Export / Import

- **Reset** — mengembalikan seluruh nilai ke `ThemeService::defaults()`.
- **Export** — mengunduh file `theme-YYYYMMDD-His.json` berisi `name`, `exported_at`, `settings`.
- **Import** — mengunggah JSON hasil export; nilai ditimpa lalu cache disegarkan.

Contoh struktur JSON export:

```json
{
  "name": "Tema Perpustakaan",
  "exported_at": "2026-07-12T12:00:00+07:00",
  "settings": [
    { "group": "color", "key": "primary_color", "value": "#4361ee", "type": "color" }
  ]
}
```

---

## 12. Pengujian

```bash
php artisan test tests/Unit/ThemeServiceTest.php
php artisan test tests/Feature/ThemeManagerTest.php
```

- **Unit** — `ThemeService`: get/set, default, cache, reset, export/import.
- **Feature** — akses halaman, simpan warna (AJAX), upload logo, penolakan file besar,
  preview tanpa persist, reset, export, import, serta otorisasi peran (403 untuk non-admin,
  redirect untuk guest).

---

## 13. Berkas Terkait

| Lapisan        | Berkas                                                        |
|----------------|---------------------------------------------------------------|
| Migration      | `database/migrations/2026_07_12_000001_create_theme_settings_table.php` |
| Model          | `app/Models/ThemeSetting.php`                                 |
| Service        | `app/Services/ThemeService.php`                               |
| Helper         | `app/Support/helpers.php`                                     |
| Middleware     | `app/Http/Middleware/LoadTheme.php`                           |
| Controller     | `app/Http/Controllers/ThemeController.php`                    |
| Request        | `app/Http/Requests/UpdateThemeRequest.php`, `ImportThemeRequest.php` |
| Policy         | `app/Policies/ThemePolicy.php`                                |
| Routes         | `routes/modules/system.php`                                   |
| Seeder         | `database/seeders/ThemeSettingSeeder.php`                     |
| Blade          | `resources/views/system/theme/index.blade.php` + `partials/field.blade.php` |
| CSS            | `resources/css/theme.css`                                     |
| JS             | `public/js/theme-manager.js`, `resources/js/theme-manager.js` |
| Tests          | `tests/Unit/ThemeServiceTest.php`, `tests/Feature/ThemeManagerTest.php` |
