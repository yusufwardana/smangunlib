<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ImportThemeRequest
 *
 * Validasi impor tema dari file JSON hasil export.
 */
class ImportThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'kepala_perpustakaan']) ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:5120', 'mimetypes:application/json,text/plain,text/json'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required'  => 'Silakan pilih file JSON tema.',
            'file.mimetypes' => 'File harus berupa JSON tema yang valid.',
            'file.max'       => 'Ukuran file maksimal 5 MB.',
        ];
    }
}
