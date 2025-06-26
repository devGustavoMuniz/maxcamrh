<?php

namespace App\Providers;

use App\Models\Franchise;
use App\Observers\FranchiseObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Franchise::observe(FranchiseObserver::class);
    }
}
