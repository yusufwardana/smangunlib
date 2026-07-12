<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'kepala_perpustakaan']);
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'max:80'],
            'folder' => ['nullable', 'string', 'max:120'],
            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'max:51200', 'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov'],
        ];
    }
}
