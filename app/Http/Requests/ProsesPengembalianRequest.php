<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProsesPengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'detail' => 'required|array',
            'detail.*.kembalikan' => 'nullable|boolean',
            'detail.*.kondisi_kembali' => 'required_with:detail.*.kembalikan|in:baik,rusak,hilang'
        ];
    }
}
