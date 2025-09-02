<?php

use App\Enums\UserRole;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('unauthenticated user cannot see franchises list', function () {
    $response = $this->get('/franchises');

    $response->assertRedirect('/login');
});

test('authenticated admin user can see franchises list', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($user)->get('/franchises');

    $response->assertStatus(200);
});

test('authenticated franchise user cannot see franchises list', function () {
    $user = User::factory()->create(['role' => UserRole::FRANCHISE]);

    $response = $this->actingAs($user)->get('/franchises');

    $response->assertStatus(403);
});

test('authenticated collaborator user cannot see franchises list', function () {
    $user = User::factory()->create(['role' => UserRole::COLLABORATOR]);

    $response = $this->actingAs($user)->get('/franchises');

    $response->assertStatus(403);
});

test('authenticated client user cannot see franchises list', function () {
    $user = User::factory()->create(['role' => UserRole::CLIENT]);

    $response = $this->actingAs($user)->get('/franchises');

    $response->assertStatus(403);
});

test('admin can create a new franchise', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);
    $franchiseData = [
        'name' => 'New Franchise',
        'email' => 'franchise@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'cnpj' => '12.345.678/0001-99',
        'phone' => '11987654321',
        'maxcam_email' => 'maxcam@example.com',
        'max_client' => 100,
        'contract_start_date' => '2024-01-01',
        'actuation_region' => 'São Paulo',
        'observations' => 'Test observations',
        'document_file' => UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
    ];

    $response = $this->actingAs($user)->post('/franchises', $franchiseData);

    $response->assertRedirect('/franchises');
    $this->assertDatabaseHas('franchises', ['cnpj' => '12.345.678/0001-99']);
    $this->assertDatabaseHas('users', ['email' => 'franchise@example.com']);
});

test('admin cannot create a new franchise with invalid data', function () {
    $user = User::factory()->create(['role' => UserRole::ADMIN]);

    $response = $this->actingAs($user)->post('/franchises', []);

    $response->assertSessionHasErrors(['name', 'email', 'password', 'cnpj', 'maxcam_email', 'max_client', 'contract_start_date', 'actuation_region']);
});

test('admin can update a franchise', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $franchise = Franchise::factory()->create();
    $updatedData = [
        'name' => 'Updated Franchise Name',
        'email' => $franchise->user->email,
        'cnpj' => $franchise->cnpj,
        'phone' => '11999999999',
        'maxcam_email' => $franchise->maxcam_email,
        'max_client' => $franchise->max_client,
        'contract_start_date' => $franchise->contract_start_date,
        'actuation_region' => $franchise->actuation_region,
        'observations' => 'Updated observations',
        'document_file' => UploadedFile::fake()->create('updated_document.pdf', 500, 'application/pdf'),
    ];

    $response = $this->actingAs($admin)->put("/franchises/{$franchise->id}", $updatedData);

    $response->assertRedirect('/franchises');
    $this->assertDatabaseHas('users', ['id' => $franchise->user_id, 'name' => 'Updated Franchise Name']);
});

test('admin cannot update a franchise with invalid data', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $franchise = Franchise::factory()->create();

    $response = $this->actingAs($admin)->put("/franchises/{$franchise->id}", ['name' => '']);

    $response->assertSessionHasErrors(['name']);
});

test('admin can delete a franchise', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $franchise = Franchise::factory()->create();

    $response = $this->actingAs($admin)->delete("/franchises/{$franchise->id}");

    $response->assertRedirect('/franchises');
    $this->assertDatabaseMissing('franchises', ['id' => $franchise->id]);
    $this->assertDatabaseMissing('users', ['id' => $franchise->user_id]);
});