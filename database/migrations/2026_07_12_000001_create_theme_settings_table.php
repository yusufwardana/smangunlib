<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();          // general, color, logo, favicon, login, dashboard, landing, typography, layout, custom, ...
            $table->string('key');                      // primary_color, logo_sekolah, font_family, ...
            $table->text('value')->nullable();
            $table->string('type')->default('string');  // string, boolean, integer, json, color, image, css, js
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
