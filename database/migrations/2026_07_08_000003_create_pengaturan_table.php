<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('description')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
            // No soft delete for settings as they are core configuration
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
