<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
  public function definition(): array
  {
    return [
      'user_id' => User::factory(['role' => UserRole::CLIENT]),
      'cnpj' => fake()->unique()->numerify('##############'),
      'test_number' => fake()->numerify('TEST###'),
      'contract_end_date' => fake()->dateTimeBetween('now', '+2 years'),
      'is_monthly_contract' => fake()->boolean(),
      'phone' => fake()->phoneNumber(),
      'logo_url' => null,
    ];
  }
}
