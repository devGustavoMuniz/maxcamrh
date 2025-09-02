<?php

namespace App\Policies;

use App\Models\Collaborator;
use App\Models\User;
use App\Enums\UserRole;

class CollaboratorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::FRANCHISE,
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Collaborator $collaborator): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::FRANCHISE) {
            return $collaborator->client?->franchise_id === $user->franchise?->id;
        }

        if ($user->role === UserRole::CLIENT) {
            return $collaborator->client_id === $user->client?->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            UserRole::ADMIN,
            UserRole::FRANCHISE,
            UserRole::CLIENT,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Collaborator $collaborator): bool
    {
        return $this->view($user, $collaborator);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Collaborator $collaborator): bool
    {
        return $this->view($user, $collaborator);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Collaborator $collaborator): bool
    {
        return $this->view($user, $collaborator);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Collaborator $collaborator): bool
    {
        return $this->view($user, $collaborator);
    }
}
