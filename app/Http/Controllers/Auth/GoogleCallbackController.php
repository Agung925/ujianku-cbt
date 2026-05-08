<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Stancl\Tenancy\Facades\Tenancy;

class GoogleCallbackController extends Controller
{
    /**
     * Redirect user ke Google OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google OAuth untuk role guru.
     */
    public function handleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google gagal. Silakan coba lagi.',
            ]);
        }

        $tenantId = tenancy()->tenant?->id;

        if ($tenantId === null) {
            return redirect()->route('login')->withErrors([
                'email' => 'Tenant context tidak ditemukan untuk login guru.',
            ]);
        }

        $guru = Guru::query()->where('email', $googleUser->getEmail())->first();

        if ($guru === null) {
            $guru = Guru::query()->create([
                'tenant_id' => $tenantId,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'nama' => $googleUser->getName() ?: 'Guru Baru',
                'foto_profil' => $googleUser->getAvatar(),
                'is_wali_kelas' => false,
                'is_active' => true,
            ]);
        } else {
            $guru->update([
                'google_id' => $googleUser->getId(),
                'foto_profil' => $googleUser->getAvatar(),
            ]);
        }

        $user = $guru->user;

        if ($user === null) {
            $user = User::query()->create([
                'name' => $guru->nama,
                'email' => $guru->email,
                'password' => Hash::make(Str::random(32)),
                'is_active' => true,
            ]);

            $guru->update(['user_id' => $user->id]);
        }

        if (! $user->hasRole('guru')) {
            $user->assignRole('guru');
        }

        Auth::guard('web')->login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('guru.dashboard'));
    }
}
