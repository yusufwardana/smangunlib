<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramLiterasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nama_program' => 'required|string|max:255',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'deskripsi' => 'nullable|string',
            'target_baca' => 'required|integer|min:1',
            'status' => 'required|in:aktif,selesai,draft'
        ];
    }
}
