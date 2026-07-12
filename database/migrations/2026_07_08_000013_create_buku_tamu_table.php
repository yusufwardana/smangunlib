<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_tamu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->enum('tipe_pengunjung', ['siswa', 'guru', 'umum'])->index();
            $table->string('no_identitas', 50)->nullable()->comment('NIS/NIP/NIK');
            $table->string('tujuan_kunjungan', 255);
            $table->dateTime('tanggal_kunjungan')->useCurrent()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_tamu');
    }
};
