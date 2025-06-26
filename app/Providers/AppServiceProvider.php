<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Franchise;
use App\Observers\ClientObserver;
use App\Observers\FranchiseObserver;
use App\Observers\CollaboratorObserver;
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
        Client::observe(ClientObserver::class);
        Collaborator::observe(CollaboratorObserver::class);
    }
}
