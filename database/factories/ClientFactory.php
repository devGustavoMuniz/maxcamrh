<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
  public function definition(): array
  {
    return [
      'cnpj' => fake()->unique()->numerify('##############'),
      'test_number' => fake()->numerify('TEST###'),
      'contract_end_date' => fake()->dateTimeBetween('now', '+2 years'),
      'is_monthly_contract' => fake()->boolean(),
      'phone' => fake()->phoneNumber(),
      'logo_url' => fake()->imageUrl(100, 100, 'business'),
    ];
  }
}
