<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Protected by middleware on route, but we can double check
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,arsip',
            'file_dokumen' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Max 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'file_dokumen.required' => 'File dokumen wajib diunggah.',
            'file_dokumen.mimes' => 'Format file tidak valid. Gunakan PDF, DOCX, JPG, atau PNG.',
            'file_dokumen.max' => 'Ukuran file maksimal adalah 10 MB.',
        ];
    }
}
