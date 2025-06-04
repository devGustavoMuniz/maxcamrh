<?php

namespace Database\Factories;

use App\Models\Franchise;
use App\Models\User; // Necessário se for criar um User aqui
use Illuminate\Database\Eloquent\Factories\Factory;

class FranchiseFactory extends Factory
{
  public function definition(): array
  {
    return [
      // user_id será preenchido se usar ->for($user) ou se o UserFactory.franchiseUser() o criar
      'maxcam_email' => fake()->unique()->companyEmail(),
      'cnpj' => fake()->unique()->numerify('##############'), // 14 números
      'max_client' => fake()->numberBetween(10, 1000),
      'contract_start_date' => fake()->dateTimeBetween('-1 year', 'now'),
      'actuation_region' => fake()->city(),
      'document_url' => fake()->url(), // Ou fake()->url() se quiser um placeholder
      'observations' => fake()->sentence(),
    ];
  }

  // Se você quiser que a FranchiseFactory também crie o User associado (alternativa ao UserFactory)
  public function configure(): static
  {
    return $this->afterCreating(function (Franchise $franchise) {
      if (!$franchise->user_id) {
        // Esta lógica é melhor no UserFactory.franchiseUser() para manter a criação do User centralizada
        // Mas é uma opção.
        // $user = User::factory()->state(['role' => UserRole::FRANCHISE])->create();
        // $franchise->user_id = $user->id;
        // $franchise->save();
      }
    });
  }
}
