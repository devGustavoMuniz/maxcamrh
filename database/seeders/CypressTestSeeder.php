<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Franchise;
use App\Models\Client;
use App\Models\Collaborator;
use App\Models\Address;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CypressTestSeeder extends Seeder
{
    /**
     * Run the database seeds for Cypress tests.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@maxcamrh.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);

        Address::create([
            'user_id' => $admin->id,
            'cep' => '01000-000',
            'street' => 'Rua Admin',
            'number' => '100',
            'complement' => 'Sala 1',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        // Create Franchise User
        $franchiseUser = User::create([
            'name' => 'Franchise User',
            'email' => 'franchise@maxcamrh.com',
            'password' => Hash::make('password'),
            'role' => UserRole::FRANCHISE,
            'email_verified_at' => now(),
        ]);

        $franchise = Franchise::create([
            'user_id' => $franchiseUser->id,
            'maxcam_email' => 'franchise@maxcamrh.com',
            'cnpj' => '12345678000190',
            'max_client' => 50,
            'contract_start_date' => now()->subMonths(6),
            'actuation_region' => 'São Paulo',
            'observations' => 'Franchise de teste para Cypress',
        ]);

        Address::create([
            'user_id' => $franchiseUser->id,
            'cep' => '01310-100',
            'street' => 'Avenida Paulista',
            'number' => '1000',
            'complement' => 'Sala 101',
            'neighborhood' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        // Create Client User
        $clientUser = User::create([
            'name' => 'Client User',
            'email' => 'client@maxcamrh.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENT,
            'email_verified_at' => now(),
        ]);

        $client = Client::create([
            'user_id' => $clientUser->id,
            'franchise_id' => $franchise->id,
            'cnpj' => '98765432000110',
            'phone' => '(21) 3333-4444',
        ]);

        Address::create([
            'user_id' => $clientUser->id,
            'cep' => '22250-040',
            'street' => 'Rua dos Clients',
            'number' => '200',
            'complement' => 'Sala 202',
            'neighborhood' => 'Botafogo',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
        ]);

        // Create Collaborator User
        $collaboratorUser = User::create([
            'name' => 'Collaborator User',
            'email' => 'collaborator@maxcamrh.com',
            'password' => Hash::make('password'),
            'role' => UserRole::COLLABORATOR,
            'email_verified_at' => now(),
        ]);

        $collaboratorAddress = Address::create([
            'user_id' => $collaboratorUser->id,
            'cep' => '30130-000',
            'street' => 'Rua dos Colaboradores',
            'number' => '300',
            'neighborhood' => 'Savassi',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
        ]);

        $collaborator = Collaborator::create([
            'user_id' => $collaboratorUser->id,
            'client_id' => $client->id,
            'address_id' => $collaboratorAddress->id,
            'cpf' => '12345678900',
            'date_of_birth' => '1990-01-15',
            'cellphone' => '(31) 99999-9999',
            'department' => 'TI',
            'position' => 'Developer',
            'admission_date' => now()->subMonths(1),
            'observations' => 'Colaborador de teste para Cypress',
        ]);

        $this->command->info('✓ Cypress test users created successfully!');
        $this->command->info('  - Admin: admin@maxcamrh.com');
        $this->command->info('  - Franchise: franchise@maxcamrh.com');
        $this->command->info('  - Client: client@maxcamrh.com');
        $this->command->info('  - Collaborator: collaborator@maxcamrh.com');
        $this->command->info('  Password for all: password');
    }
}
