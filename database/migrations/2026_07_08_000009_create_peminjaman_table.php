<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 50)->unique();
            $table->foreignId('anggota_id')->constrained('anggota')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Pustakawan yang melayani');
            $table->date('tanggal_pinjam')->index();
            $table->date('due_date')->index();
            $table->enum('status', ['aktif', 'selesai', 'terlambat'])->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
