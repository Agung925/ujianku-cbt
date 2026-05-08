<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-gray-800">Login Siswa</h1>
        <p class="text-sm text-gray-500">Masuk menggunakan NIS dan password.</p>
    </div>

    <form method="POST" action="{{ route('siswa.login.store') }}">
        @csrf

        <div>
            <x-input-label for="nis" :value="__('NIS')" />
            <x-text-input id="nis" class="block mt-1 w-full" type="text" name="nis" :value="old('nis')" required autofocus />
            <x-input-error :messages="$errors->get('nis')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Login guru/admin</a>

            <x-primary-button>
                {{ __('Masuk Siswa') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
