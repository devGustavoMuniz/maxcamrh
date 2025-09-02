<?php

use App\Enums\UserRole;
use App\Models\Collaborator;
use App\Models\User;
use App\Models\Client;

test('unauthenticated user cannot see collaborators list', function () {
    $response = $this->get('/collaborators');

    $response->assertRedirect('/login');
});

test('authenticated admin user can see collaborators list', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($user)->get('/collaborators');

    $response->assertStatus(200);
});

test('authenticated franchise user can see collaborators list', function () {
    $user = User::factory()->create(['role' => UserRole::FRANCHISE]);

    $response = $this->actingAs($user)->get('/collaborators');

    $response->assertStatus(200);
});

test('authenticated client user cannot see collaborators list', function () {
    $user = User::factory()->create(['role' => UserRole::CLIENT]);

    $response = $this->actingAs($user)->get('/collaborators');

    $response->assertStatus(403);
});

test('non-admin/non-franchise users cannot create collaborators', function () {
    $user = User::factory()->create(['role' => UserRole::COLLABORATOR]); // Or CLIENT
    $this->actingAs($user);

    $collaboratorData = [
        'user' => [
            'name' => 'Unauthorized Collaborator',
            'email' => 'unauth_collab@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'collaborator' => [
            'date_of_birth' => '1990-01-01',
            'gender' => 'Masculino',
            'marital_status' => 'Solteiro(a)',
            'scholarity' => 'Superior Completo',
            'father_name' => 'Pai do Colaborador',
            'mother_name' => 'Mae do Colaborador',
            'nationality' => 'Brasileira',
            'personal_email' => 'personal@example.com',
            'business_email' => 'business@example.com',
            'phone' => '11987654321',
            'cellphone' => '11987654321',
            'emergency_phone' => '11987654321',
            'department' => 'TI',
            'position' => 'Desenvolvedor',
            'type_of_contract' => 'CLT',
            'salary' => 5000.00,
            'admission_date' => '2020-01-01',
            'direct_superior_name' => 'Superior Teste',
            'hierarchical_degree' => 'Analista',
            'observations' => 'Nenhuma',
            'contract_start_date' => '2020-01-01',
            'contract_expiration' => '2025-01-01',
            'cpf' => '111.111.111-11',
            'rg' => '11.111.111-1',
            'cnh' => '11111111111',
            'reservista' => '11111111111',
            'titulo_eleitor' => '111111111111',
            'zona_eleitoral' => '111',
            'pis_ctps_numero' => '11111111111',
            'ctps_serie' => '111111',
            'banco' => 'Banco Teste',
            'agencia' => '1111',
            'conta_corrente' => '11111-1',
        ],
        'address' => [
            'cep' => '11111-111',
            'street' => 'Rua Teste',
            'number' => '123',
            'complement' => 'Apto 1',
            'neighborhood' => 'Bairro Teste',
            'state' => 'SP',
            'city' => 'Cidade Teste',
        ],
    ];

    $response = $this->post('/collaborators', $collaboratorData);

    $response->assertStatus(403);
    $this->assertDatabaseMissing('users', ['email' => 'unauth_collab@example.com']);
});

test('admin can create a new collaborator', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);
    $client = Client::factory()->create();

    $collaboratorData = [
        'user' => [
            'name' => 'New Collaborator',
            'email' => 'new_collab@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'collaborator' => [
            'client_id' => $client->id,
            'date_of_birth' => '1990-01-01',
            'gender' => 'Masculino',
            'marital_status' => 'Solteiro(a)',
            'scholarity' => 'Superior Completo',
            'father_name' => 'Pai do Colaborador',
            'mother_name' => 'Mae do Colaborador',
            'nationality' => 'Brasileira',
            'personal_email' => 'personal_new@example.com',
            'business_email' => 'business_new@example.com',
            'phone' => '11987654321',
            'cellphone' => '11987654321',
            'emergency_phone' => '11987654321',
            'department' => 'TI',
            'position' => 'Desenvolvedor',
            'type_of_contract' => 'CLT',
            'salary' => 5000.00,
            'admission_date' => '2020-01-01',
            'direct_superior_name' => 'Superior Teste',
            'hierarchical_degree' => 'Analista',
            'observations' => 'Nenhuma',
            'contract_start_date' => '2020-01-01',
            'contract_expiration' => '2025-01-01',
            'cpf' => '111.111.111-11',
            'rg' => '11.111.111-1',
            'cnh' => '11111111111',
            'reservista' => '11111111111',
            'titulo_eleitor' => '111111111111',
            'zona_eleitoral' => '111',
            'pis_ctps_numero' => '11111111111',
            'ctps_serie' => '111111',
            'banco' => 'Banco Teste',
            'agencia' => '1111',
            'conta_corrente' => '11111-1',
        ],
        'address' => [
            'cep' => '11111-111',
            'street' => 'Rua Teste',
            'number' => '123',
            'complement' => 'Apto 1',
            'neighborhood' => 'Bairro Teste',
            'state' => 'SP',
            'city' => 'Cidade Teste',
        ],
    ];

    $response = $this->actingAs($user)->post('/collaborators', $collaboratorData);

    $response->assertRedirect('/collaborators');
    $this->assertDatabaseHas('users', ['email' => 'new_collab@example.com']);
    $this->assertDatabaseHas('collaborators', ['cpf' => '111.111.111-11']);
});

test('admin cannot create a new collaborator with invalid data', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($user)->post('/collaborators', []);

    $response->assertSessionHasErrors(['user.name', 'user.email', 'user.password']);
});

test('non-admin/non-franchise users cannot update collaborators', function () {
    $user = User::factory()->create(['role' => UserRole::COLLABORATOR]); // Or CLIENT
    $this->actingAs($user);

    $collaborator = Collaborator::factory()->create()->load('address');
    $updatedData = [
        'user' => [
            'name' => 'Attempted Update',
            'email' => $collaborator->user->email,
        ],
        'collaborator' => [
            'date_of_birth' => $collaborator->date_of_birth,
            'gender' => $collaborator->gender,
            'marital_status' => $collaborator->marital_status,
            'scholarity' => $collaborator->scholarity,
            'father_name' => $collaborator->father_name,
            'mother_name' => $collaborator->mother_name,
            'nationality' => $collaborator->nationality,
            'personal_email' => $collaborator->personal_email,
            'business_email' => $collaborator->business_email,
            'phone' => $collaborator->phone,
            'cellphone' => $collaborator->cellphone,
            'emergency_phone' => $collaborator->emergency_phone,
            'department' => $collaborator->department,
            'position' => $collaborator->position,
            'type_of_contract' => 'CLT',
            'salary' => $collaborator->salary,
            'admission_date' => $collaborator->admission_date,
            'direct_superior_name' => $collaborator->direct_superior_name,
            'hierarchical_degree' => $collaborator->hierarchical_degree,
            'observations' => $collaborator->observations,
            'contract_start_date' => $collaborator->contract_start_date,
            'contract_expiration' => $collaborator->contract_expiration,
            'cpf' => $collaborator->cpf,
            'rg' => $collaborator->rg,
            'cnh' => $collaborator->cnh,
            'reservista' => $collaborator->reservista,
            'titulo_eleitor' => $collaborator->titulo_eleitor,
            'zona_eleitoral' => $collaborator->zona_eleitoral,
            'pis_ctps_numero' => $collaborator->pis_ctps_numero,
            'ctps_serie' => $collaborator->ctps_serie,
            'banco' => $collaborator->banco,
            'agencia' => $collaborator->agencia,
            'conta_corrente' => $collaborator->conta_corrente,
        ],
        'address' => [
            'cep' => $collaborator->address->cep,
            'street' => $collaborator->address->street,
            'number' => $collaborator->address->number,
            'complement' => $collaborator->address->complement,
            'neighborhood' => $collaborator->address->neighborhood,
            'state' => $collaborator->address->state,
            'city' => $collaborator->address->city,
        ],
    ];

    $response = $this->put("/collaborators/{$collaborator->id}", $updatedData);

    $response->assertStatus(403);
    $this->assertDatabaseHas('users', ['id' => $collaborator->user_id, 'name' => $collaborator->user->name]); // Ensure not updated
});

test('admin can update a collaborator', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collaborator = Collaborator::factory()->create()->load('address');
    $updatedData = [
        'user' => [
            'name' => 'Updated Collaborator Name',
            'email' => $collaborator->user->email, // Keep email the same to avoid unique constraint issues
        ],
        'collaborator' => [
            'date_of_birth' => $collaborator->date_of_birth,
            'gender' => $collaborator->gender,
            'marital_status' => $collaborator->marital_status,
            'scholarity' => $collaborator->scholarity,
            'father_name' => $collaborator->father_name,
            'mother_name' => $collaborator->mother_name,
            'nationality' => $collaborator->nationality,
            'personal_email' => $collaborator->personal_email,
            'business_email' => $collaborator->business_email,
            'phone' => $collaborator->phone,
            'cellphone' => $collaborator->cellphone,
            'emergency_phone' => $collaborator->emergency_phone,
            'department' => $collaborator->department,
            'position' => $collaborator->position,
            'type_of_contract' => 'CLT',
            'salary' => $collaborator->salary,
            'admission_date' => $collaborator->admission_date,
            'direct_superior_name' => $collaborator->direct_superior_name,
            'hierarchical_degree' => $collaborator->hierarchical_degree,
            'observations' => $collaborator->observations,
            'contract_start_date' => $collaborator->contract_start_date,
            'contract_expiration' => $collaborator->contract_expiration,
            'cpf' => $collaborator->cpf,
            'rg' => $collaborator->rg,
            'cnh' => $collaborator->cnh,
            'reservista' => $collaborator->reservista,
            'titulo_eleitor' => $collaborator->titulo_eleitor,
            'zona_eleitoral' => $collaborator->zona_eleitoral,
            'pis_ctps_numero' => $collaborator->pis_ctps_numero,
            'ctps_serie' => $collaborator->ctps_serie,
            'banco' => $collaborator->banco,
            'agencia' => $collaborator->agencia,
            'conta_corrente' => $collaborator->conta_corrente,
        ],
        'address' => [
            'cep' => $collaborator->address->cep,
            'street' => $collaborator->address->street,
            'number' => $collaborator->address->number,
            'complement' => $collaborator->address->complement,
            'neighborhood' => $collaborator->address->neighborhood,
            'state' => $collaborator->address->state,
            'city' => $collaborator->address->city,
        ],
    ];

    $response = $this->actingAs($admin)->put("/collaborators/{$collaborator->id}", $updatedData);

    $response->assertRedirect('/collaborators');
    $this->assertDatabaseHas('users', ['id' => $collaborator->user_id, 'name' => 'Updated Collaborator Name']);
});

test('admin cannot update a collaborator with invalid data', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collaborator = Collaborator::factory()->create()->load('address');

    $response = $this->actingAs($admin)->put("/collaborators/{$collaborator->id}", ['user' => ['name' => '']]);

    $response->assertSessionHasErrors(['user.name']);
});

test('non-admin/non-franchise users cannot delete collaborators', function () {
    $user = User::factory()->create(['role' => UserRole::COLLABORATOR]); // Or CLIENT
    $this->actingAs($user);

    $collaborator = Collaborator::factory()->create()->load('address');

    $response = $this->delete("/collaborators/{$collaborator->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('collaborators', ['id' => $collaborator->id]); // Ensure not deleted
});

test('admin can delete a collaborator', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $collaborator = Collaborator::factory()->create()->load('address');

    $response = $this->actingAs($admin)->delete("/collaborators/{$collaborator->id}");

    $response->assertRedirect('/collaborators');
    $this->assertDatabaseMissing('collaborators', ['id' => $collaborator->id]);
    $this->assertDatabaseMissing('users', ['id' => $collaborator->user_id]);
});