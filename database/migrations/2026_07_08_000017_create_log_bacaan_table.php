<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_bacaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_literasi_id')->constrained('peserta_literasi')->cascadeOnDelete();
            $table->foreignId('buku_id')->nullable()->constrained('buku')->nullOnDelete()->comment('Buku dari koleksi');
            $table->string('judul_buku_luar', 255)->nullable()->comment('Buku dari luar koleksi');
            $table->date('tanggal_baca');
            $table->text('refleksi');
            $table->integer('poin_diberikan')->default(0);
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending')->index();
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_bacaan');
    }
};
