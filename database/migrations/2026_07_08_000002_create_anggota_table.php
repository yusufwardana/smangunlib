<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('nomor_anggota', 50)->unique();
            $table->enum('tipe_anggota', ['siswa', 'guru', 'tendik'])->index();
            $table->string('no_identitas', 50)->unique()->comment('NIS/NISN/NIP');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('no_telepon', 20)->nullable();
            $table->enum('status', ['aktif', 'non-aktif', 'blacklist'])->default('aktif')->index();
            $table->date('masa_berlaku_sampai');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
