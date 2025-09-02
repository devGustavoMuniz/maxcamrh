<?php

namespace App\Policies;

use App\Models\Collaborator;
use App\Models\User;
use App\Enums\UserRole;

class CollaboratorPolicy
{
    /**
     * Determine whether the user can view any models.
     * Todos os perfis podem, em teoria, acessar a listagem (a query no controller fará o filtro correto).
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
     * Admin: pode ver qualquer um.
     * Franqueado: pode ver colaboradores de clientes da sua franquia.
     * Cliente: pode ver seus próprios colaboradores.
     */
    public function view(User $user, Collaborator $collaborator): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::FRANCHISE) {
            // Verifica se o colaborador tem um cliente e se esse cliente pertence à franquia do usuário.
            return $collaborator->client?->franchise_id === $user->franchise?->id;
        }

        if ($user->role === UserRole::CLIENT) {
            return $collaborator->client_id === $user->client?->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * Admins, Franqueados e Clientes podem criar colaboradores.
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
     * Mesmas regras da visualização (view).
     */
    public function update(User $user, Collaborator $collaborator): bool
    {
        return $this->view($user, $collaborator);
    }

    /**
     * Determine whether the user can delete the model.
     * Mesmas regras da visualização (view).
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
