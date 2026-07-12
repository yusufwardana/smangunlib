<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->boolean('is_digital')->default(false)->after('cover_image')->index();
            $table->string('file_digital', 255)->nullable()->after('is_digital')->comment('File PDF untuk e-book');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn(['is_digital', 'file_digital']);
        });
    }
};
