<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_literasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_literasi_id')->constrained('program_literasi')->cascadeOnDelete();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->integer('total_poin')->default(0);
            $table->enum('status', ['aktif', 'lulus', 'gagal'])->default('aktif');
            $table->dateTime('tanggal_daftar')->useCurrent();
            $table->timestamps();
            
            $table->unique(['program_literasi_id', 'anggota_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_literasi');
    }
};
