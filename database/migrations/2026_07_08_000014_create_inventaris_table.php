<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_inventaris', 50)->unique();
            $table->string('nama_barang', 255);
            $table->string('kategori_barang', 100);
            $table->integer('jumlah');
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'tidak_layak'])->default('baik');
            $table->year('tahun_pengadaan')->nullable();
            $table->string('sumber_dana', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
