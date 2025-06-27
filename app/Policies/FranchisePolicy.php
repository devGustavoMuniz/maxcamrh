<?php

namespace App\Policies;

use App\Models\Franchise;
use App\Models\User;
use App\Enums\UserRole;

class FranchisePolicy
{
    /**
     * Determine whether the user can view any models.
     * Apenas Admins podem listar todos os franqueados.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     * Um Admin pode ver qualquer franqueado. Um Franqueado só pode ver a si mesmo.
     */
    public function view(User $user, Franchise $franchise): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::FRANCHISE) {
            // Garante que o usuário tem um franqueado associado e que é o mesmo sendo visualizado.
            return $user->franchise?->id === $franchise->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * Apenas Admins podem criar novos franqueados.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     * Um Admin pode editar qualquer franqueado. Um Franqueado só pode editar a si mesmo.
     */
    public function update(User $user, Franchise $franchise): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::FRANCHISE) {
            return $user->franchise?->id === $franchise->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Apenas Admins podem deletar franqueados.
     */
    public function delete(User $user, Franchise $franchise): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Franchise $franchise): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Franchise $franchise): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
