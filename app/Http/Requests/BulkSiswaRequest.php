<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkSiswaRequest extends FormRequest
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
        return [
            'siswas'              => ['required', 'array', 'min:1', 'max:50'],
            'siswas.*.nis'        => ['required', 'string', 'max:20', 'distinct'],
            'siswas.*.nama'       => ['required', 'string', 'max:255'],
            'siswas.*.kelas'      => ['required', 'string', 'max:50'],
            'siswas.*.email'      => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'siswas.required'          => 'Data siswa wajib diisi.',
            'siswas.*.nis.required'    => 'NIS pada baris :position wajib diisi.',
            'siswas.*.nis.distinct'    => 'Ada NIS yang duplikat dalam daftar.',
            'siswas.*.nama.required'   => 'Nama pada baris :position wajib diisi.',
            'siswas.*.kelas.required'  => 'Kelas pada baris :position wajib diisi.',
        ];
    }
}
