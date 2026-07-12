<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_updates', function (Blueprint $table) {
            $table->id();
            $table->string('versi_lama', 50)->nullable();
            $table->string('versi_baru', 50);
            $table->text('changelog')->nullable();
            $table->string('checksum', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'extracting', 'success', 'failed', 'rolled_back'])->default('pending');
            $table->text('log')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_updates');
    }
};
