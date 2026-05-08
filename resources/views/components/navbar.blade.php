{{-- resources/views/components/navbar.blade.php --}}
<div class="navbar bg-base-100 shadow-sm border-b border-base-300 sticky top-0 z-40">
    <div class="navbar-start">
        {{-- Mobile drawer toggle --}}
        <label for="main-drawer" class="btn btn-ghost btn-circle drawer-button lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </label>
        <span class="font-bold text-primary ml-2 hidden lg:block">{{ config('app.name', 'UJIANKU-CBT') }}</span>
    </div>

    <div class="navbar-center lg:hidden">
        <span class="font-bold text-primary">{{ config('app.name', 'UJIANKU-CBT') }}</span>
    </div>

    <div class="navbar-end gap-2">
        {{-- Notifications --}}
        <button class="btn btn-ghost btn-circle">
            <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
        </button>

        {{-- User Dropdown --}}
        @auth
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                <div class="bg-primary text-primary-content rounded-full w-8">
                    <span class="text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li class="menu-title">
                    <span>{{ auth()->user()->name }}</span>
                    <span class="text-xs text-base-content/50">{{ auth()->user()->email }}</span>
                </li>
                <li><a href="{{ route('profile.edit') }}">Profil</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left text-error">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </div>
</div>
