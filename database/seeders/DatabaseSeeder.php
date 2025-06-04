<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Franchise;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Address; // Se for criar endereços avulsos, mas vamos integrar
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // 1. Criar um Admin
    User::factory()->admin()->create([
      'name' => 'Admin Principal',
      'email' => 'admin@maxcamrh.com', // Email fixo para fácil acesso
    ]);

    // 2. Criar alguns Franqueados (cada um já cria seu User e Address via UserFactory.franchiseUser)
    $franchises = User::factory()->franchiseUser()->count(5)->create()->map(function ($user) {
      return $user->franchise; // Pegar a instância de Franchise
    });
    // Se UserFactory.franchiseUser() não criar o User e o Address, você faria:
    // $franchises = Franchise::factory()->count(5)
    //     ->for(User::factory()->state(['role' => UserRole::FRANCHISE]))
    //     ->has(Address::factory()->count(1), 'user.addresses') // Se Address é para User da Franchise
    //     ->create();


    // 3. Criar Clientes para esses Franqueados
    // Cada cliente também já cria seu User e Address via UserFactory.clientUser
    foreach ($franchises as $franchise) {
      User::factory()->clientUser($franchise)->count(rand(3, 10))->create()->map(function($user){
        return $user->client; // Pegar a instância de Client
      })->each(function ($client) {
        // 4. Criar Colaboradores para cada Cliente
        // Cada colaborador também já cria seu User e Address via UserFactory.collaboratorUser
        User::factory()->collaboratorUser($client)->count(rand(5, 15))->create();
      });
    }

    // Opcional: Criar alguns usuários clientes e colaboradores "soltos" se a lógica da factory permitir
    // User::factory()->clientUser()->count(3)->create();
    // User::factory()->collaboratorUser()->count(5)->create();

    // Se você tiver outros seeders individuais, pode chamá-los aqui:
    // $this->call([
    //     OutroSeeder::class,
    // ]);
  }
}
