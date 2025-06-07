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
      'password' => static::$password ??= Hash::make('password'),
      'remember_token' => Str::random(10),
      'role' => UserRole::CLIENT->value,
    ];
  }

  public function unverified(): static
  {
    return $this->state(fn (array $attributes) => [
      'email_verified_at' => null,
    ]);
  }

  public function admin(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::ADMIN->value,
    ])->afterCreating(function (User $user) {
      Address::factory()->for($user)->create();
    });
  }

  public function franchiseUser(): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::FRANCHISE->value,
    ])->afterCreating(function (User $user) {
      Franchise::factory()->for($user)->create();
      Address::factory()->for($user)->create();
    });
  }

  public function clientUser(Franchise $franchise = null): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::CLIENT->value,
    ])->afterCreating(function (User $user) use ($franchise) {
      Client::factory()
        ->for($user)
        ->for($franchise ?? Franchise::factory()->franchiseUser()->create()->franchise, 'franchise')
        ->create();
      Address::factory()->for($user)->create();
    });
  }

  public function collaboratorUser(Client $client = null): static
  {
    return $this->state(fn (array $attributes) => [
      'role' => UserRole::COLLABORATOR->value,
    ])->afterCreating(function (User $user) use ($client) {
      Collaborator::factory()
        ->for($user)
        ->for($client ?? Client::factory()->clientUser()->create()->client, 'client')
        ->create();
      Address::factory()->for($user)->create();
    });
  }
}
