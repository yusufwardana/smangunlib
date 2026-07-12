<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_literasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program', 255);
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->text('deskripsi')->nullable();
            $table->integer('target_baca')->comment('Target jumlah buku');
            $table->enum('status', ['aktif', 'selesai', 'draft'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_literasi');
    }
};
