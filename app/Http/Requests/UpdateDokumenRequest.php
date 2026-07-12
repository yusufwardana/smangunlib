<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,arsip',
            'file_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Optional on update
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul dokumen wajib diisi.',
            'file_dokumen.mimes' => 'Format file tidak valid. Gunakan PDF, DOCX, JPG, atau PNG.',
            'file_dokumen.max' => 'Ukuran file maksimal adalah 10 MB.',
        ];
    }
}
