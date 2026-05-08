<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class KategoriUjianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'urutan' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori ujian harus diisi',
            'nama.max' => 'Nama kategori tidak boleh lebih dari 255 karakter',
            'deskripsi.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal harus 1',
        ];
    }
}
