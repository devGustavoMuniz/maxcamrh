<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
      return $user->role === 'admin' || $user->role === 'franchise';
    }

    public function view(User $user, Client $client): bool
    {
      if ($user->role === 'admin') return true;
      return $user->role === 'franchise' && $client->franchise_id === $user->franchise->id;
    }

    public function create(User $user): bool
    {
      return $user->role === 'admin' || $user->role === 'franchise';
    }

    public function update(User $user, Client $client): bool
    {
      if ($user->role === 'admin') return true;
      return $user->role === 'franchise' && $client->franchise_id === $user->franchise->id;
    }

    public function delete(User $user, Client $client): bool
    {
      if ($user->role === 'admin') return true;
      return $user->role === 'franchise' && $client->franchise_id === $user->franchise->id;
    }

    public function restore(User $user, Client $client): bool
    {
      return false;
    }

    public function forceDelete(User $user, Client $client): bool
    {
      return false;
    }
}
