<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadSystemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'update_file' => ['required', 'file', 'mimes:zip', 'max:50000'],
        ];
    }
}
