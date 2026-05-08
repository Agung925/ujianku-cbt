<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siswaId = $this->route('siswa');

        return [
            'nis'       => [
                'required',
                'string',
                'max:20',
                Rule::unique('siswas', 'nis')->ignore($siswaId)->whereNull('deleted_at'),
            ],
            'nama'      => ['required', 'string', 'max:255'],
            'kelas'     => ['required', 'string', 'max:50'],
            'email'     => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('siswas', 'email')->ignore($siswaId)->whereNull('deleted_at'),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'   => 'NIS wajib diisi.',
            'nis.unique'     => 'NIS sudah terdaftar.',
            'nama.required'  => 'Nama siswa wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'email.unique'   => 'Email sudah terdaftar.',
        ];
    }
}
