<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandingContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:60'],
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'icon' => ['nullable', 'string', 'max:120'],
            'image' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,mp4'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:120'],
            'content_date' => ['nullable', 'date'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,active,inactive,published,archived'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
