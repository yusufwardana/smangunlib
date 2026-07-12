<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key', 100)->unique();
            $table->string('nama_sekolah', 255)->nullable();
            $table->string('domain', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->date('tanggal_aktivasi')->nullable();
            $table->date('expired_date')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'invalid'])->default('invalid');
            $table->string('versi_aplikasi', 50)->nullable();
            $table->integer('max_user')->default(0);
            $table->integer('max_storage_mb')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
