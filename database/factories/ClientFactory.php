<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use App\Models\Franchise;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
  public function definition(): array
  {
    return [
      // user_id e franchise_id serão preenchidos via ->for()
      'cnpj' => fake()->unique()->numerify('##############'),
      'test_number' => fake()->numerify('TEST###'),
      'contract_end_date' => fake()->dateTimeBetween('now', '+2 years'),
      'is_monthly_contract' => fake()->boolean(),
      'phone' => fake()->phoneNumber(),
      'logo_url' => fake()->imageUrl(100, 100, 'business'), // Ou fake()->imageUrl(100, 100, 'business') se quiser placeholders
    ];
  }
}
