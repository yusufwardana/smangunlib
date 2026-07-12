<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('eksemplar_id')->constrained('eksemplar')->restrictOnDelete();
            $table->date('tanggal_kembali')->nullable();
            $table->enum('kondisi_kembali', ['baik', 'rusak', 'hilang'])->nullable();
            $table->enum('status', ['dipinjam', 'dikembalikan', 'hilang'])->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
