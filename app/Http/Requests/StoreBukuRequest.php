<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'isbn' => 'nullable|string|max:20|unique:buku,isbn',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:150',
            'tahun_terbit' => 'required|digits:4|integer',
            'edisi' => 'nullable|string|max:50',
            'halaman' => 'nullable|integer',
            'bahasa' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'rak_lokasi_id' => 'nullable|exists:rak_lokasi,id',
            'kategori_ids' => 'nullable|array',
            'kategori_ids.*' => 'exists:kategori,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'file_digital' => 'nullable|file|mimes:pdf|max:15360', // Max 15MB
        ];
    }
}
