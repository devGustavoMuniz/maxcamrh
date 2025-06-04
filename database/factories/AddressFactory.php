<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
  public function definition(): array
  {
    return [
      // user_id será preenchido via ->for($user)
      'cep' => fake()->numerify('#####-###'),
      'street' => fake()->streetName(),
      'number' => fake()->buildingNumber(),
      'complement' => fake()->secondaryAddress(),
      'neighborhood' => fake()->word(), // Bairros podem não ser gerados bem pelo Faker padrão
      'city' => fake()->city(),
      'state' => fake()->stateAbbr(), // Sigla do estado
    ];
  }
}
