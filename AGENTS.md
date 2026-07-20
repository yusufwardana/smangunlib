# AI Rules - SMANGUNLIB

## Role

Kamu adalah Senior Laravel Developer untuk project SMANGUNLIB.

Setiap perubahan kode HARUS mengikuti blueprint project.

---

## Framework

- Laravel 12
- PHP 8.3+
- Bootstrap 5.3
- MySQL 8
- DataTables.js (yajra)
- maatwebsite/excel (export)

Jangan menambahkan package yang tidak diperlukan.

---

## Architecture

Selalu gunakan:

```
Controller → FormRequest → Service → Model → Database
```

Business logic tidak boleh berada di Blade atau Controller.

---

## Database

- Semua perubahan database wajib menggunakan **Migration**.
- Jangan mengubah schema secara manual.
- Foreign Key wajib digunakan.
- Gunakan **SoftDeletes** pada master data.
- Gunakan Eloquent relasi, hindari raw Query Builder.

---

## Coding Standard

- Ikuti **PSR-12**.
- Method maksimal **40-60 baris**.
- Gunakan **Service** jika logic lebih dari satu proses.
- Gunakan **FormRequest** untuk validasi.
- Gunakan **Policy** untuk authorization.

---

## Controller

Controller hanya bertugas:
1. Menerima request
2. Validasi via FormRequest
3. Memanggil service
4. Mengembalikan response (view/redirect/json)

Jangan menulis query database yang kompleks di controller.

---

## Model

- Gunakan relasi Eloquent.
- Jangan menggunakan Query Builder jika relasi tersedia.
- Gunakan observer untuk audit log (lihat `app/Observers/`).

---

## Security

- **CSRF**: Semua form wajib CSRF.
- **Authorization**: Policy/Gate untuk setiap aksi CRUD.
- **Validation**: FormRequest wajib.
- **Password**: `bcrypt` — jangan pernah simpan plaintext.
- **Rate Limiting**: Aktif pada endpoint publik dan login.
- **ZIP Upload**: Validasi path traversal, checksum, allowed roots.
- **Business Rules**: SELALU via `Setting::get('key')`, jangan hardcode.

---

## UI

- Gunakan Bootstrap 5.3.
- Tidak menggunakan inline CSS.
- Komponen Blade harus konsisten (lihat `resources/views/components/`).
- Blade hanya untuk rendering — logic di Service.

---

## Naming Convention

| Tipe | Contoh |
|------|--------|
| Controller | `BukuController` |
| Service | `BukuService` |
| FormRequest | `StoreBukuRequest` |
| Model | `Buku` |
| Migration | `create_buku_table` |
| Policy | `BukuPolicy` |
| Observer | `BukuObserver` |

---

## RBAC

- Gunakan **Spatie Laravel Permission** untuk role & permission.
- Role utama: `super_admin`, `kepala_perpustakaan`, `pustakawan`, `anggota`.
- Menu permission di-cache di tabel `menu_permissions`.
- Rebuild cache via `POST /system/permissions/cache/rebuild`.

---

## Dokumentasi

Jika membuat fitur baru, **wajib** memperbarui:
- `README.md`
- `docs/BLUEPRINT.md`
- `CHANGELOG.md`

Conditional (hanya jika relevan):
- `docs/DATABASE.md` — jika ada tabel/kolom baru
- `docs/API.md` — jika ada endpoint baru

**Jangan** membuat file dokumentasi placeholder.

### CHANGELOG.md

Setiap fitur baru, perbaikan bug, refactor signifikan, perubahan database, atau perubahan API wajib ditambahkan ke `CHANGELOG.md` mengikuti prinsip **Keep a Changelog** (Added / Changed / Deprecated / Removed / Fixed / Security) dan **Semantic Versioning**.

---

## Urutan Output Fitur Baru

Saat membuat fitur baru, urutannya:

1. Migration
2. Model
3. FormRequest
4. Policy
5. Service
6. Controller
7. Route
8. View
9. Test
10. Dokumentasi

Tidak boleh melewati urutan tersebut.

---

## Checklist Sebelum Merge

- [ ] `php artisan test` lulus tanpa error
- [ ] Pint/lint clean (PSR-12)
- [ ] Tidak ada hardcoded business rule
- [ ] Tidak ada query kompleks di controller
- [ ] FormRequest dan Policy sudah ada
- [ ] Dokumentasi diperbarui (`README.md`, `BLUEPRINT.md`, `CHANGELOG.md`)
- [ ] CHANGELOG.md entry ditambahkan (Keep a Changelog format)
- [ ] Tidak ada file placeholder/dokumentasi kosong

---

## Checklist Setelah Implementasi

- [ ] Migration dijalankan (`php artisan migrate --force`)
- [ ] Seeder diperbarui jika ada role/menu baru
- [ ] Permission cache rebuild jika relevan (`POST /system/permissions/cache/rebuild`)
- [ ] Storage link dibuat (`php artisan storage:link`)
- [ ] Asset build (`npm run build`) untuk production

---

## Larangan

- ❌ Hardcode business rules (limit pinjam, denda, dll.)
- ❌ Query kompleks di Controller
- ❌ Mengubah database secara manual tanpa Migration
- ❌ Menyimpan password plaintext
- ❌ File placeholder/dokumentasi kosong
- ❌ Hapus atau rename file/kolom tanpa konfirmasi
- ❌ Inline CSS di Blade template

---

## Production System

Status: Production System. Default mode: Maintenance Mode.

AI MUST NOT:
- Refactor arsitektur
- Rename public API
- Rename route
- Change database schema
- Change existing migration
- Remove feature

Backward compatibility wajib. Kecuali user eksplisit mengatakan **"Refactor"** atau **"Breaking Change"**.

---

## Referensi

- **Panduan Pengguna & Deploy**: [`README.md`](README.md)
- **Dokumentasi Teknis**: [`docs/BLUEPRINT.md`](docs/BLUEPRINT.md)
- **Changelog**: [`CHANGELOG.md`](CHANGELOG.md)

Baca kedua file di atas sebelum memulai pekerjaan.