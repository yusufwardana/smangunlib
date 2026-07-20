<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration modul Pengaturan Hak Akses Menu (Menu Permission / RBAC).
 *
 * Melengkapi tabel bawaan Spatie (roles, permissions, role_has_permissions)
 * dengan tiga tabel baru:
 *
 *  - menus              : pohon menu (tree) yang seluruhnya dikontrol database.
 *  - menu_permissions   : daftar aksi (view/create/edit/…) yang tersedia per menu.
 *  - permission_has_menu: pemetaan setiap permission Spatie ke menu pemiliknya,
 *                         sehingga sidebar & middleware dapat membaca hak akses.
 *
 * Tidak ada fitur lama yang diubah; hanya penambahan tabel.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Pohon menu (dikontrol penuh melalui database, tidak di-hardcode).
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();
            $table->string('key')->unique();      // contoh: koleksi.buku, sirkulasi.peminjaman
            $table->string('title');              // label tampil di sidebar
            $table->string('icon')->nullable();   // kelas FontAwesome
            $table->string('route')->nullable();  // nama route (jika ada)
            $table->string('url')->nullable();    // url manual (jika ada)
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'sort']);
        });

        // 2. Aksi/hak akses yang tersedia untuk tiap menu (mendorong matriks checkbox UI).
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->string('action');  // view, create, edit, delete, approve, export_pdf, ...
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['menu_id', 'action']);
        });

        // 3. Pemetaan permission -> menu (satu permission dimiliki oleh satu menu).
        Schema::create('permission_has_menu', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->string('action');

            $table->primary('permission_id');
            $table->index(['menu_id', 'action']);

            $table->foreign('permission_id')
                ->references('id')
                ->on(config('permission.table_names.permissions', 'permissions'))
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_has_menu');
        Schema::dropIfExists('menu_permissions');
        Schema::dropIfExists('menus');
    }
};
