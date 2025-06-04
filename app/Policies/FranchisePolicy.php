<?php

namespace App\Policies;

use App\Models\Franchise;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FranchisePolicy
{
  public function viewAny(User $user): bool
  {
    return false;
  }

  public function view(User $user, Franchise $franchise): bool
  {
    if ($user->role === 'franchise') {
      return $user->franchise && $franchise->id === $user->franchise->id;
    }
    return false;
  }

  public function create(User $user): bool
  {
    return false;
  }

  public function update(User $user, Franchise $franchise): bool
  {
    if ($user->role === 'franchise') {
      return $user->franchise && $franchise->id === $user->franchise->id;
    }
    return false;
  }

  public function delete(User $user, Franchise $franchise): bool
  {
    return false;
  }

  public function restore(User $user, Franchise $franchise): bool
  {
    return false;
  }

  public function forceDelete(User $user, Franchise $franchise): bool
  {
    return false;
  }
}
