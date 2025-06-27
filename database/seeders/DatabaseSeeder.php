<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

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

        if (!App::environment('production')) {
            $franchises = User::factory()->franchiseUser()->count(5)->create()->map(function ($user) {
                return $user->franchise;
            });

            foreach ($franchises as $franchise) {
                User::factory()->clientUser($franchise)->count(rand(3, 10))->create()->map(function ($user) {
                    return $user->client;
                })->each(function ($client) {
                    User::factory()->collaboratorUser($client)->count(rand(5, 15))->create();
                });
            }
        }
    }
}
