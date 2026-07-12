<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eksemplar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->restrictOnDelete();
            $table->string('nomor_barcode', 50)->unique();
            $table->date('tanggal_pengadaan');
            $table->string('asal_pengadaan', 100);
            $table->decimal('harga', 12, 2)->nullable();
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->index();
            $table->enum('status_sirkulasi', ['tersedia', 'dipinjam', 'weeding'])->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eksemplar');
    }
};
