<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Franchise;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Address;
use App\Enums\UserRole; // Importe seu Enum UserRole
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
  protected static ?string $password;

  public function definition(): array
  {
    return [
      'name' => fake()->name(),
      'email' => fake()->unique()->safeEmail(),
      'email_verified_at' => now(),
      'password' => static::$password ??= Hash::make('password'), // Senha padrão 'password'
      'remember_token' => Str::random(10),
      'role' => UserRole::CLIENT->value, // Papel padrão, pode ser sobrescrito
    ];
  }

  public function unverified(): static
  {
    return $this->state(fn (array $attributes) => [
      'email_verified_at' => null,
    ]);
  }

  // Estado para criar um ADMIN
  public function admin(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::ADMIN->value,
    ])->afterCreating(function (User $user) {
      // Admin pode ter um endereço também
      Address::factory()->for($user)->create();
    });
  }

  // Estado para criar um usuário FRANQUEADO e sua FRANQUIA associada
  public function franchiseUser(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::FRANCHISE->value,
    ])->afterCreating(function (User $user) {
      Franchise::factory()->for($user)->create(); // Cria a franquia associada
      Address::factory()->for($user)->create(); // Cria um endereço para o usuário franqueado
    });
  }

  // Estado para criar um usuário CLIENTE e seu CLIENT associado (precisará de uma franquia)
  public function clientUser(Franchise $franchise = null): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::CLIENT->value,
    ])->afterCreating(function (User $user) use ($franchise) {
      Client::factory()
        ->for($user)
        ->for($franchise ?? Franchise::factory()->franchiseUser()->create()->franchise, 'franchise') // Garante uma franquia
        ->create();
      Address::factory()->for($user)->create(); // Cria um endereço para o usuário cliente
    });
  }

  // Estado para criar um usuário COLABORADOR e seu COLABORATOR associado (precisará de um cliente)
  public function collaboratorUser(Client $client = null): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::COLLABORATOR->value,
    ])->afterCreating(function (User $user) use ($client) {
      Collaborator::factory()
        ->for($user)
        // Garante um cliente: Cria um novo cliente (com seu user e franquia) se não for passado
        ->for($client ?? Client::factory()->clientUser()->create()->client, 'client')
        ->create();
      Address::factory()->for($user)->create(); // Cria um endereço para o usuário colaborador
    });
  }
}
