<?php

namespace Database\Factories;

use App\Models\Collaborator;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollaboratorFactory extends Factory
{
  public function definition(): array
  {
    return [
      'photo_url' => fake()->imageUrl(100,100,'people'),
      'curriculum_url' => fake()->url(),
      'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years'),
      'gender' => fake()->randomElement(['Masculino', 'Feminino', 'Outro']),
      'is_special_needs_person' => fake()->boolean(10),
      'marital_status' => fake()->randomElement(['Solteiro(a)', 'Casado(a)', 'Divorciado(a)', 'Viúvo(a)']),
      'scholarity' => fake()->randomElement(['Ensino Médio Completo', 'Superior Incompleto', 'Superior Completo', 'Pós-graduação']),
      'father_name' => fake()->name('male'),
      'mother_name' => fake()->name('female'),
      'nationality' => fake()->country(),
      'personal_email' => fake()->unique()->safeEmail(),
      'business_email' => fake()->unique()->safeEmail(),
      'phone' => fake()->phoneNumber(),
      'cellphone' => fake()->phoneNumber(),
      'emergency_phone' => fake()->phoneNumber(),
      'department' => fake()->jobTitle(),
      'position' => fake()->jobTitle(),
      'type_of_contract' => fake()->randomElement(['CLT', 'PJ', 'Estágio']),
      'salary' => fake()->randomFloat(2, 1500, 15000),
      'admission_date' => fake()->dateTimeBetween('-3 years', 'now'),
      'direct_superior_name' => fake()->name(),
      'hierarchical_degree' => fake()->word(),
      'observations' => fake()->paragraph(),
      'contract_start_date' => fake()->dateTimeThisYear(),
      'contract_expiration' => fake()->dateTimeBetween('now', '+2 years'),
      'cpf' => fake()->unique()->numerify('###########'),
      'rg' => fake()->unique()->numerify('#######'),
      'cnh' => fake()->numerify('###########'),
      'reservista' => fake()->numerify('############'),
      'titulo_eleitor' => fake()->numerify('############'),
      'zona_eleitoral' => fake()->numerify('###'),
      'pis_ctps_numero' => fake()->numerify('###########'),
      'ctps_serie' => fake()->numerify('#####-##'),
      'banco' => fake()->company(),
      'agencia' => fake()->numerify('####-#'),
      'conta_corrente' => fake()->numerify('#####-#'),
    ];
  }
}
