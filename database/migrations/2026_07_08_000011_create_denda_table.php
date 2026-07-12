<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_peminjaman_id')->unique()->constrained('detail_peminjaman')->restrictOnDelete();
            $table->foreignId('anggota_id')->constrained('anggota')->restrictOnDelete();
            $table->integer('jumlah_hari_terlambat');
            $table->decimal('tarif_per_hari', 10, 2);
            $table->decimal('total_denda', 10, 2);
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas', 'waived'])->index();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Pustakawan penerima');
            $table->text('alasan_waive')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda');
    }
};
