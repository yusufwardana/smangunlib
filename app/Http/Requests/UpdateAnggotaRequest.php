<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Anggota;

class UpdateAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $anggotaId = $this->route('anggotum'); // Resource controller typical binding
        $anggota = Anggota::find($anggotaId);
        $userId = $anggota ? $anggota->user_id : null;

        return [
            // User Data
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8',
            
            // Anggota Data
            'tipe_anggota' => 'required|in:siswa,guru,tendik',
            'no_identitas' => 'required|string|max:50|unique:anggota,no_identitas,' . $anggotaId,
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_telepon' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,non-aktif,blacklist',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
