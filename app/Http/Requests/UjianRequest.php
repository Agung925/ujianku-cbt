<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UjianRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('guru');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'judul'             => ['required', 'string', 'max:255'],
            'deskripsi'         => ['nullable', 'string', 'max:1000'],
            'kategori_ujian_id' => ['required', 'integer', 'exists:kategori_ujians,id'],
            'tgl_mulai'         => ['required', 'date', 'after_or_equal:now'],
            'tgl_selesai'       => ['required', 'date', 'after:tgl_mulai'],
            'waktu_durasi'      => ['required', 'integer', 'min:1', 'max:480'],
            'is_acak_soal'      => ['boolean'],
            'is_acak_opsi'      => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required'             => 'Judul ujian wajib diisi.',
            'judul.max'                  => 'Judul ujian maksimal 255 karakter.',
            'kategori_ujian_id.required' => 'Kategori ujian wajib dipilih.',
            'kategori_ujian_id.exists'   => 'Kategori ujian tidak ditemukan.',
            'tgl_mulai.required'         => 'Tanggal mulai wajib diisi.',
            'tgl_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh di masa lalu.',
            'tgl_selesai.required'       => 'Tanggal selesai wajib diisi.',
            'tgl_selesai.after'          => 'Tanggal selesai harus setelah tanggal mulai.',
            'waktu_durasi.required'      => 'Durasi ujian wajib diisi.',
            'waktu_durasi.min'           => 'Durasi ujian minimal 1 menit.',
            'waktu_durasi.max'           => 'Durasi ujian maksimal 480 menit (8 jam).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_acak_soal' => $this->boolean('is_acak_soal'),
            'is_acak_opsi' => $this->boolean('is_acak_opsi'),
        ]);
    }
}
