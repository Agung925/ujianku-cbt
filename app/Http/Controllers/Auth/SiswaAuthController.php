<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SiswaLoginRequest;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SiswaAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.siswa-login');
    }

    /**
     * Login siswa via NIS + password (session-based, tanpa guard web).
     */
    public function login(SiswaLoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $siswa = Siswa::query()
            ->where('nis', $validated['nis'])
            ->where('is_active', true)
            ->first();

        if ($siswa === null || ! Hash::check($validated['password'], $siswa->password)) {
            return back()->withErrors([
                'nis' => 'NIS atau password salah.',
            ])->withInput($request->only('nis'));
        }

        $request->session()->regenerate();
        $request->session()->put('siswa_auth', [
            'id' => $siswa->id,
            'tenant_id' => $siswa->tenant_id,
            'nis' => $siswa->nis,
            'nama' => $siswa->nama,
            'logged_in_at' => now()->toDateTimeString(),
        ]);

        return redirect()->intended(route('siswa.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('siswa_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login');
    }
}
