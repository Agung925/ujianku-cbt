<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SoalImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('guru') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'kategori_ujian_id' => 'required|integer|exists:kategori_ujians,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // max 5MB
        ];
    }

    /**
     * Get custom messages
     */
    public function messages(): array
    {
        return [
            'kategori_ujian_id.required' => 'Kategori ujian harus dipilih',
            'kategori_ujian_id.exists' => 'Kategori ujian tidak ditemukan',
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
            'file.max' => 'File tidak boleh lebih dari 5MB',
        ];
    }
}
