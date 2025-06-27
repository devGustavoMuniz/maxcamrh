<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
  public function definition(): array
  {
    return [
      'cep' => fake()->numerify('#####-###'),
      'street' => fake()->streetName(),
      'number' => fake()->buildingNumber(),
      'complement' => fake()->secondaryAddress(),
      'neighborhood' => fake()->word(),
      'city' => fake()->city(),
      'state' => fake()->stateAbbr(),
    ];
  }
}
