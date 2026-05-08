<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user?->hasRole('guru') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'kategori_ujian_id' => ['required', 'exists:kategori_ujians,id'],
            'pertanyaan' => ['required', 'string', 'max:5000'],
            'tipe_soal' => ['required', 'in:pilihan_ganda,essay'],
            'bobot' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'is_active' => ['boolean'],
        ];

        // Validasi khusus untuk pilihan ganda
        if ($this->input('tipe_soal') === 'pilihan_ganda') {
            $rules = array_merge($rules, [
                'opsi_a' => ['required', 'string', 'max:1000'],
                'opsi_b' => ['required', 'string', 'max:1000'],
                'opsi_c' => ['required', 'string', 'max:1000'],
                'opsi_d' => ['required', 'string', 'max:1000'],
                'kunci_jawaban' => ['required', 'in:a,b,c,d'],
            ]);
        }

        // Validasi khusus untuk essay
        if ($this->input('tipe_soal') === 'essay') {
            $rules = array_merge($rules, [
                'kunci_jawaban' => ['required', 'string', 'max:2000'],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'kategori_ujian_id.required' => 'Kategori ujian harus dipilih',
            'kategori_ujian_id.exists' => 'Kategori ujian yang dipilih tidak ditemukan',
            'pertanyaan.required' => 'Pertanyaan soal harus diisi',
            'pertanyaan.max' => 'Pertanyaan tidak boleh lebih dari 5000 karakter',
            'tipe_soal.required' => 'Tipe soal harus dipilih',
            'tipe_soal.in' => 'Tipe soal harus pilihan ganda atau essay',
            'bobot.required' => 'Bobot soal harus diisi',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0.1',
            'bobot.max' => 'Bobot maksimal 100',
            'opsi_a.required' => 'Opsi A harus diisi',
            'opsi_b.required' => 'Opsi B harus diisi',
            'opsi_c.required' => 'Opsi C harus diisi',
            'opsi_d.required' => 'Opsi D harus diisi',
            'kunci_jawaban.required' => 'Kunci jawaban harus diisi',
            'kunci_jawaban.in' => 'Kunci jawaban harus A, B, C, atau D',
        ];
    }
}
