<?php

namespace Database\Factories;

use App\Models\Franchise;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranchiseFactory extends Factory
{
  public function definition(): array
  {
    return [
      'maxcam_email' => fake()->unique()->companyEmail(),
      'cnpj' => fake()->unique()->numerify('##############'),
      'max_client' => fake()->numberBetween(10, 1000),
      'contract_start_date' => fake()->dateTimeBetween('-1 year', 'now'),
      'actuation_region' => fake()->city(),
      'document_url' => fake()->url(),
      'observations' => fake()->sentence(),
    ];
  }

  public function configure(): static
  {
    return $this->afterCreating(function (Franchise $franchise) {
    });
  }
}
