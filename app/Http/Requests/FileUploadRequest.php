<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->route('type', 'photo');

        if ($type === 'logo') {
            return [
                'file' => ['required', 'file', 'max:1024', 'mimes:jpg,jpeg,png,svg'],
            ];
        }

        return [
            'file' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File wajib diupload.',
            'file.max'      => 'Ukuran file melebihi batas maksimum.',
            'file.mimes'    => 'Format file tidak didukung. Gunakan JPG, PNG' . (str_contains($this->route('type', ''), 'logo') ? ', atau SVG.' : '.'),
        ];
    }
}
