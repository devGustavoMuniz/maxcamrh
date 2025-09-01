<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

test('unauthenticated user cannot see clients list', function () {
    $response = $this->get('/clients');

    $response->assertRedirect('/login');
});

test('authenticated admin user can see clients list', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($user)->get('/clients');

    $response->assertStatus(200);
});

test('authenticated franchise user can see clients list', function () {
    $user = User::factory()->create(['role' => UserRole::FRANCHISE]);

    $response = $this->actingAs($user)->get('/clients');

    $response->assertStatus(200);
});

test('admin can create a new client', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);
    $clientData = [
        'name' => 'New Client',
        'email' => 'client@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'cnpj' => '12.345.678/0001-99',
        'is_monthly_contract' => true,
    ];

    $response = $this->actingAs($user)->post('/clients', $clientData);

    $response->assertRedirect('/clients');
    $this->assertDatabaseHas('clients', ['cnpj' => '12.345.678/0001-99']);
    $this->assertDatabaseHas('users', ['email' => 'client@example.com']);
});

test('admin cannot create a new client with invalid data', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($user)->post('/clients', []);

    $response->assertSessionHasErrors(['name', 'email', 'password', 'cnpj', 'is_monthly_contract']);
});

test('admin can update a client', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $client = Client::factory()->create();
    $updatedData = [
        'name' => 'Updated Client Name',
        'email' => $client->user->email, // Keep email the same to avoid unique constraint issues
        'cnpj' => $client->cnpj, // Keep cnpj the same
        'is_monthly_contract' => $client->is_monthly_contract,
    ];

    $response = $this->actingAs($admin)->put("/clients/{$client->id}", $updatedData);

    $response->assertRedirect('/clients');
    $this->assertDatabaseHas('users', ['id' => $client->user_id, 'name' => 'Updated Client Name']);
});

test('admin cannot update a client with invalid data', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $client = Client::factory()->create();

    $response = $this->actingAs($admin)->put("/clients/{$client->id}", ['name' => '']);

    $response->assertSessionHasErrors(['name']);
});

test('admin can delete a client', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $client = Client::factory()->create();

    $response = $this->actingAs($admin)->delete("/clients/{$client->id}");

    $response->assertRedirect('/clients');
    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    $this->assertDatabaseMissing('users', ['id' => $client->user_id]);
});
