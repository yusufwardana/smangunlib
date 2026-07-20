<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateThemeRequest
 *
 * Validasi penyimpanan pengaturan tema per-grup, termasuk upload aset.
 * Batas upload 5 MB; format yang didukung: PNG, JPG, SVG, WEBP, ICO
 * (dan MP4/WEBM untuk video background login).
 */
class UpdateThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }


    public function rules(): array
    {
        return [
            'group'      => ['required', 'string', 'max:80'],
            'settings'   => ['nullable', 'array'],
            'settings.*' => ['nullable'],

            'uploads'    => ['nullable', 'array'],
            // Gambar / ikon: 5 MB, format aman.
            'uploads.*'  => [
                'nullable',
                'file',
                'max:5120',
                'mimes:png,jpg,jpeg,svg,webp,ico,mp4,webm',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'uploads.*.max'   => 'Ukuran file maksimal 5 MB.',
            'uploads.*.mimes' => 'Format yang didukung: PNG, JPG, SVG, WEBP, ICO, MP4, WEBM.',
        ];
    }

    /**
     * Normalisasi checkbox: field boolean yang tidak dicentang tidak terkirim,
     * sehingga controller yang menentukan default. Di sini kita biarkan apa adanya.
     */
    public function settings(): array
    {
        return $this->input('settings', []) ?? [];
    }
}
