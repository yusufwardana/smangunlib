<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi_literasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_literasi_id')->constrained('program_literasi')->cascadeOnDelete();
            $table->enum('tipe_file', ['foto', 'pdf']);
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_literasi');
    }
};
