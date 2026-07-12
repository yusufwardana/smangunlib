<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_administrasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            
            // Kategori Dokumen Legalitas
            $table->enum('kategori_dokumen', [
                'sk_kepala', 
                'sk_petugas', 
                'struktur_organisasi', 
                'program_kerja', 
                'visi_misi', 
                'tata_tertib', 
                'sop', 
                'jadwal_layanan', 
                'denah_perpustakaan'
            ])->index();

            $table->string('file_path', 255);
            $table->string('tipe_file', 50)->comment('pdf, docx, png, dll');
            $table->integer('ukuran_file')->comment('dalam kilobytes (KB)');
            
            $table->enum('status', ['aktif', 'arsip'])->default('aktif')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Uploader');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_administrasi');
    }
};
