<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('judul', 255);
            $table->string('pengarang', 255);
            $table->string('penerbit', 150);
            $table->year('tahun_terbit');
            $table->string('edisi', 50)->nullable();
            $table->integer('halaman')->nullable();
            $table->string('bahasa', 50);
            $table->text('deskripsi')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->foreignId('rak_lokasi_id')->nullable()->constrained('rak_lokasi')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add Full Text Search Index for opac searching using raw DB statement (except for SQLite)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE buku ADD FULLTEXT INDEX buku_judul_pengarang_fulltext (judul, pengarang)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
