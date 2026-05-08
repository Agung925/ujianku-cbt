<?php

namespace App\Providers;

use App\Models\JawabanSiswa;
use App\Models\Soal;
use App\Observers\JawabanSiswaObserver;
use App\Policies\SoalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register authorization policies
        Gate::policy(Soal::class, SoalPolicy::class);

        // Register model observers
        JawabanSiswa::observe(JawabanSiswaObserver::class);
    }
}
