<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'anggota_id' => 'required|exists:anggota,id',
            'eksemplar_ids' => 'required|array|min:1',
            'eksemplar_ids.*' => 'exists:eksemplar,id',
            'keterangan' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'anggota_id.required' => 'Pilih anggota peminjam.',
            'eksemplar_ids.required' => 'Pilih minimal satu buku untuk dipinjam.'
        ];
    }
}
