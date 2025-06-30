<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use App\Models\Event;
use App\Policies\EventPolicy;
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
        Vite::prefetch(concurrency: 3);
      //  Gate::policy(Event::class, EventPolicy::class);
    }
}
