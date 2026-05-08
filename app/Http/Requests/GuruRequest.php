<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guruId = $this->route('guru');

        return [
            'nama'          => ['required', 'string', 'max:255'],
            'email'         => [
                'required',
                'email',
                'max:255',
                Rule::unique('gurus', 'email')->ignore($guruId),
            ],
            'nip'           => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('gurus', 'nip')->ignore($guruId),
            ],
            'is_wali_kelas' => ['nullable', 'boolean'],
            'is_active'     => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'  => 'Nama guru wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
            'nip.unique'     => 'NIP sudah terdaftar.',
        ];
    }
}
