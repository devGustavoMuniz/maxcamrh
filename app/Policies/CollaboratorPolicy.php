<?php

namespace App\Policies;

use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CollaboratorPolicy
{
  public function viewAny(User $user): bool
  {
    return $user->role === 'franchise' || $user->role === 'client';
  }

  public function view(User $user, Collaborator $collaborator): bool
  {
    if ($user->role === 'franchise') {
      return $collaborator->client && $user->franchise && $collaborator->client->franchise_id === $user->franchise->id;
    }

    if ($user->role === 'client') {
      return $user->client && $collaborator->client_id === $user->client->id;
    }

    return false;
  }

  public function create(User $user): bool
  {
    return $user->role === 'franchise' || $user->role === 'client';
  }

  public function update(User $user, Collaborator $collaborator): bool
  {
    if ($user->role === 'franchise') {
      return $collaborator->client && $user->franchise && $collaborator->client->franchise_id === $user->franchise->id;
    }

    if ($user->role === 'client') {
      return $user->client && $collaborator->client_id === $user->client->id;
    }

    return false;
  }

  public function delete(User $user, Collaborator $collaborator): bool
  {
    if ($user->role === 'franchise') {
      return $collaborator->client && $user->franchise && $collaborator->client->franchise_id === $user->franchise->id;
    }

    if ($user->role === 'client') {
      return $user->client && $collaborator->client_id === $user->client->id;
    }

    return false;
  }

  public function restore(User $user, Collaborator $collaborator): bool
  {
    return $this->update($user, $collaborator);
  }

  public function forceDelete(User $user, Collaborator $collaborator): bool
  {
    return $this->delete($user, $collaborator);
  }
}
