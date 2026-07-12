<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->index();
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('subtitle')->nullable();
            $table->longText('body')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable()->index();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('attachment')->nullable();
            $table->string('video_url')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('author')->nullable();
            $table->date('content_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('landing_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('landing_menus')->nullOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 80)->nullable()->index();
            $table->string('folder', 120)->nullable()->index();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('landing_menus');
        Schema::dropIfExists('landing_contents');
    }
};
