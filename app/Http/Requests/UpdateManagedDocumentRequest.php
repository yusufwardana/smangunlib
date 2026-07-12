<?php

namespace App\Http\Requests;

class UpdateManagedDocumentRequest extends StoreManagedDocumentRequest
{
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'kategori_dokumen' => ['required', 'string', 'max:80'],
            'versi' => ['required', 'string', 'max:50'],
            'masa_berlaku_sampai' => ['nullable', 'date'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:10240'],
        ];
    }
}
