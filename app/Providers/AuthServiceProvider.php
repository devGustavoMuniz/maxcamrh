<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Franchise;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\CollaboratorPolicy;
use App\Policies\FranchisePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
  protected $policies = [
    Client::class => ClientPolicy::class,
    Collaborator::class => CollaboratorPolicy::class,
    Franchise::class => FranchisePolicy::class,
    User::class => UserPolicy::class,
  ];

  public function boot(): void
  {
    $this->registerPolicies();

    Gate::before(function (User $user, string $ability) {
       if ($user->role === 'admin') {
           return true;
       }
       return null;
    });

    Gate::define('access-clients-module', function (User $user) {
       return $user->role === 'admin' || $user->role === 'franchise';
    });

    Gate::define('access-collaborators-module', function (User $user) {
       return $user->role === 'admin' || $user->role === 'franchise' || $user->role === 'client';
    });

    Gate::define('access-franchises-module', function (User $user) {
       return $user->role === 'admin';
    });

    Gate::define('access-admins-module', function (User $user) {
       return $user->role === 'admin';
    });
  }
}
