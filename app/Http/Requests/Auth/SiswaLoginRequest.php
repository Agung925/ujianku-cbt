<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Facades\Tenancy;

class SiswaLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validasi login siswa via NIS + password.
     */
    public function rules(): array
    {
        $tenantId = tenancy()->tenant?->id;

        $nisRule = Rule::exists('siswas', 'nis')->where(function ($query) use ($tenantId) {
            if ($tenantId !== null) {
                $query->where('tenant_id', $tenantId);
            }

            $query->whereNull('deleted_at');
        });

        return [
            'nis' => ['required', 'string', $nisRule],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS wajib diisi.',
            'nis.exists' => 'NIS tidak ditemukan untuk tenant ini.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ];
    }
}
