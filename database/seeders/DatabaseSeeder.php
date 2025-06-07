<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Franchise;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Address; // Se for criar endereços avulsos, mas vamos integrar
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    User::factory()->admin()->create([
      'name' => 'Admin Principal',
      'email' => 'admin@maxcamrh.com',
      'password' => 'password',
      'role' => 'admin'
    ]);

    $franchises = User::factory()->franchiseUser()->count(5)->create()->map(function ($user) {
      return $user->franchise; // Pegar a instância de Franchise
    });

    foreach ($franchises as $franchise) {
      User::factory()->clientUser($franchise)->count(rand(3, 10))->create()->map(function($user){
        return $user->client;
      })->each(function ($client) {
        User::factory()->collaboratorUser($client)->count(rand(5, 15))->create();
      });
    }
  }
}
