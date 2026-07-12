<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandingMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:landing_menus,id'],
            'name' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
