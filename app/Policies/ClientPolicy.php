<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Enums\UserRole;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     * Admins e Franqueados podem listar clientes.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::FRANCHISE;
    }

    /**
     * Determine whether the user can view the model.
     * Admin: pode ver qualquer cliente.
     * Franqueado: pode ver clientes da sua franquia.
     * Cliente: pode ver a si mesmo.
     */
    public function view(User $user, Client $client): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::FRANCHISE) {
            return $user->franchise?->id === $client->franchise_id;
        }

        if ($user->role === UserRole::CLIENT) {
            return $user->client?->id === $client->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * Admins e Franqueados podem criar novos clientes.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::FRANCHISE;
    }

    /**
     * Determine whether the user can update the model.
     * Mesmas regras da visualização (view).
     */
    public function update(User $user, Client $client): bool
    {
        return $this->view($user, $client);
    }

    /**
     * Determine whether the user can delete the model.
     * Apenas Admins podem deletar clientes.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
