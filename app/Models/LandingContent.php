<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LandingContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'subtitle',
        'body',
        'description',
        'category',
        'icon',
        'image',
        'attachment',
        'video_url',
        'button_text',
        'button_url',
        'author',
        'content_date',
        'published_at',
        'status',
        'sort_order',
        'seo_title',
        'seo_description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'content_date' => 'date',
            'published_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LandingContent $content) {
            if (! $content->slug && $content->title) {
                $content->slug = Str::slug($content->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'published']);
    }
}
