<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
  public function viewAny(User $user): bool
  {
    return false;
  }

  public function view(User $user, User $model): bool
  {
    if ($user->id === $model->id) {
      return true;
    }
    return false;
  }

  public function create(User $user): bool
  {
    return false;
  }

  public function update(User $user, User $model): bool
  {
    if ($user->id === $model->id) {
      return true;
    }
    return false;
  }

  public function delete(User $user, User $model): bool
  {
    if ($user->id === $model->id) {
      return false;
    }
    return false;
  }

  public function restore(User $user, User $model): bool
  {
    return false;
  }

  public function forceDelete(User $user, User $model): bool
  {
    return $this->delete($user, $model);
  }
}
