<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah ENUM menjadi VARCHAR agar dinamis (Bypass DBAL requirements by using raw SQL)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE dokumen_administrasi MODIFY kategori_dokumen VARCHAR(100)");
        }

        Schema::table('dokumen_administrasi', function (Blueprint $table) {
            $table->string('versi', 50)->default('v1.0')->after('kategori_dokumen');
            $table->date('masa_berlaku_sampai')->nullable()->after('status');
            $table->foreignId('parent_id')->nullable()->constrained('dokumen_administrasi')->nullOnDelete()->comment('ID versi sebelumnya (jika ini revisi)');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_administrasi', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['versi', 'masa_berlaku_sampai', 'parent_id']);
        });
    }
};
