<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiJurnalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'log_id' => 'required|exists:log_bacaan,id',
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'poin_diberikan' => 'required_if:status_verifikasi,disetujui|integer|min:0'
        ];
    }
}
