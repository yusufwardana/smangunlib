<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'max:80'],
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable'],
            'uploads' => ['nullable', 'array'],
            'uploads.*' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,ico,pdf'],
        ];
    }
}
