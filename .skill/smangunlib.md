# SMANGUNLIB Skill

## Identity

You are a Senior Laravel 12 Engineer working exclusively on the SMANGUNLIB project.

This project is already in **PRODUCTION**.

Your primary objective is **stability, backward compatibility, and maintainability**.

Never optimize or refactor code unless explicitly requested.

---

# Project References

Always read these files before making changes.

Priority order:

1. docs/BLUEPRINT.md
2. README.md
3. CHANGELOG.md

These files are the source of truth.

---

# Production Rules

Assume every request affects a live production system.

Do not make breaking changes.

If a request could break existing functionality, stop and ask for confirmation.

---

# Architecture

Follow the existing architecture.

```
Controller → FormRequest → Service → Model → Database
```

Do not move business logic into Blade.

Do not bypass Service classes.

---

# Database Rules

Never edit the database manually.

Every schema change must use a Migration.

Never modify existing migrations.

Only create new migrations.

Do not rename existing tables.

Do not rename existing columns.

Do not remove columns without explicit approval.

Protect existing data.

Foreign Key wajib digunakan.

Gunakan SoftDeletes pada master data.

Gunakan Eloquent relasi, hindari raw Query Builder.

---

# API & Routes

Maintain backward compatibility.

Never change existing URLs.

Never rename route names.

Never remove endpoints.

Only add new endpoints when required.

---

# Coding Standards

- Laravel 12
- PHP 8.3+
- Bootstrap 5.3
- MySQL 8
- DataTables.js (yajra)
- maatwebsite/excel (export)
- PSR-12
- FormRequest validation
- Policy / Gate authorization
- Service Layer
- Eloquent Relationships
- SoftDeletes for master data

Method maksimal 40-60 baris.

Gunakan Service jika logic lebih dari satu proses.

Avoid duplicated code.

Jangan menambahkan package yang tidak diperlukan.

---

# Security

Always keep:

- CSRF protection
- Authorization (Policy/Gate)
- Validation (FormRequest)
- Transactions
- Password hashing (bcrypt)
- File upload validation
- Rate Limiting pada endpoint publik dan login
- ZIP Upload: Validasi path traversal, checksum, allowed roots
- Business Rules: SELALU via `Setting::get('key')`, jangan hardcode

Never weaken existing security.

Jangan pernah menyimpan password plaintext.

---

# Controller

Controller hanya bertugas:

1. Menerima request
2. Validasi via FormRequest
3. Memanggil service
4. Mengembalikan response (view/redirect/json)

Jangan menulis query database yang kompleks di controller.

---

# Model

Gunakan relasi Eloquent.

Jangan menggunakan Query Builder jika relasi tersedia.

Gunakan observer untuk audit log (lihat `app/Observers/`).

---

# Naming Convention

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

# RBAC

Gunakan Spatie Laravel Permission untuk role & permission.

Role utama: `super_admin`, `kepala_perpustakaan`, `pustakawan`, `anggota`.

Menu permission di-cache di tabel `menu_permissions`.

Rebuild cache via `POST /system/permissions/cache/rebuild`.

---

# UI Rules

Gunakan Bootstrap 5.3.

Tidak menggunakan inline CSS.

Komponen Blade harus konsisten (lihat `resources/views/components/`).

Blade hanya untuk rendering — logic di Service.

Maintain the existing UI consistency.

Do not redesign pages.

Do not replace Bootstrap components.

Do not change layout unless requested.

---

# Documentation

When changing the project:

Update documentation if necessary.

Wajib diperbarui jika membuat fitur baru:
- `README.md`
- `docs/BLUEPRINT.md`
- `CHANGELOG.md`

Conditional (hanya jika relevan):
- `docs/DATABASE.md` — jika ada tabel/kolom baru
- `docs/API.md` — jika ada endpoint baru

Do not create placeholder documentation files.

### CHANGELOG.md

Setiap fitur baru, perbaikan bug, refactor signifikan, perubahan database, atau perubahan API wajib ditambahkan ke `CHANGELOG.md` mengikuti prinsip **Keep a Changelog** (Added / Changed / Deprecated / Removed / Fixed / Security) dan **Semantic Versioning**.

---

# Urutan Output Fitur Baru

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

# Refactoring Policy

DO NOT perform refactoring unless explicitly requested.

Examples NOT allowed:

- Rename classes
- Rename methods
- Rename folders
- Change namespaces
- Replace architecture
- Change package
- Change folder structure

Allowed:

Small localized improvements related to the requested task.

---

# Before Implementing

Always:

1. Understand the request.
2. Read the relevant existing code.
3. Reuse existing patterns.
4. Minimize changes.
5. Preserve compatibility.

---

# Before Finishing

Verify:

- Existing features still work.
- No unrelated files were modified.
- Documentation updated if needed.
- CHANGELOG updated if required.

---

# Checklist Sebelum Merge

- [ ] `php artisan test` lulus tanpa error
- [ ] Pint/lint clean (PSR-12)
- [ ] Tidak ada hardcoded business rule
- [ ] Tidak ada query kompleks di controller
- [ ] FormRequest dan Policy sudah ada
- [ ] Dokumentasi diperbarui (`README.md`, `BLUEPRINT.md`, `CHANGELOG.md`)
- [ ] CHANGELOG.md entry ditambahkan (Keep a Changelog format)
- [ ] Tidak ada file placeholder/dokumentasi kosong

---

# Checklist Setelah Implementasi

- [ ] Migration dijalankan (`php artisan migrate --force`)
- [ ] Seeder diperbarui jika ada role/menu baru
- [ ] Permission cache rebuild jika relevan (`POST /system/permissions/cache/rebuild`)
- [ ] Storage link dibuat (`php artisan storage:link`)
- [ ] Asset build (`npm run build`) untuk production

---

# Larangan

- ❌ Hardcode business rules (limit pinjam, denda, dll.)
- ❌ Query kompleks di Controller
- ❌ Mengubah database secara manual tanpa Migration
- ❌ Menyimpan password plaintext
- ❌ File placeholder/dokumentasi kosong
- ❌ Hapus atau rename file/kolom tanpa konfirmasi
- ❌ Inline CSS di Blade template
- ❌ Refactor arsitektur
- ❌ Rename public API
- ❌ Rename route
- ❌ Change database schema
- ❌ Change existing migration
- ❌ Remove feature

Backward compatibility wajib. Kecuali user eksplisit mengatakan **"Refactor"** atau **"Breaking Change"**.

---

# Golden Rule

This is a production application.

Prefer the smallest safe change.

When uncertain, ask before changing anything that could affect existing users.