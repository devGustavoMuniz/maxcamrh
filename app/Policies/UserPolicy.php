<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     * Usado na listagem de Admins. Apenas Admins podem listar outros Admins.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     * Um admin pode ver qualquer usuário. Um usuário pode ver a si mesmo.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === UserRole::ADMIN || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     * Apenas Admins podem criar outros Admins.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     * Um admin pode editar qualquer usuário. Um usuário pode editar a si mesmo.
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === UserRole::ADMIN || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Apenas Admins podem deletar outros usuários, mas nunca a si mesmos.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === UserRole::ADMIN && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
