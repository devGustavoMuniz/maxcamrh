<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[test]
    public function unauthenticated_users_cannot_view_admins_list(): void
    {
        $response = $this->get(route('admins.index'));
        $response->assertRedirect(route('login'));
    }

    #[test]
    public function non_admin_users_cannot_view_admins_list(): void
    {
        $user = User::factory()->create(['role' => UserRole::CLIENT->value]);
        $this->actingAs($user);

        $response = $this->get(route('admins.index'));
        $response->assertStatus(403);
    }

    #[test]
    public function admin_users_can_view_admins_list(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->get(route('admins.index'));
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Admins/Index'));
    }

    #[test]
    public function admin_can_create_a_new_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $newAdminData = [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('admins.store'), $newAdminData);

        $response->assertRedirect(route('admins.index'));
        $this->assertDatabaseHas('users', ['email' => 'newadmin@example.com', 'role' => UserRole::ADMIN->value]);
    }

    #[test]
    public function admin_cannot_create_a_new_admin_with_invalid_data(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $invalidAdminData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'notmatching',
        ];

        $response = $this->post(route('admins.store'), $invalidAdminData);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
    }

    #[test]
    public function admin_can_update_an_admin(): void
    {
        $adminToUpdate = User::factory()->admin()->create();
        $actingAdmin = User::factory()->admin()->create();
        $this->actingAs($actingAdmin);

        $updatedData = [
            '_method' => 'PUT',
            'name' => 'Updated Admin Name',
            'email' => 'updated_admin@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post(route('admins.update', $adminToUpdate->id), $updatedData);

        $response->assertRedirect(route('admins.index'));
        $this->assertDatabaseHas('users', ['id' => $adminToUpdate->id, 'name' => 'Updated Admin Name', 'email' => 'updated_admin@example.com']);
    }

    #[test]
    public function admin_cannot_update_an_admin_with_invalid_data(): void
    {
        $adminToUpdate = User::factory()->admin()->create();
        $actingAdmin = User::factory()->admin()->create();
        $this->actingAs($actingAdmin);

        $invalidData = [
            '_method' => 'PUT',
            'name' => '',
            'email' => 'invalid-email',
        ];

        $response = $this->post(route('admins.update', $adminToUpdate->id), $invalidData);

        $response->assertSessionHasErrors(['name', 'email']);
        $this->assertDatabaseHas('users', ['id' => $adminToUpdate->id, 'name' => $adminToUpdate->name]);
    }

    #[test]
    public function admin_can_delete_an_admin(): void
    {
        $adminToDelete = User::factory()->admin()->create();
        $actingAdmin = User::factory()->admin()->create();
        $this->actingAs($actingAdmin);

        $response = $this->delete(route('admins.destroy', $adminToDelete->id));

        $response->assertRedirect(route('admins.index'));
        $this->assertDatabaseMissing('users', ['id' => $adminToDelete->id, 'role' => UserRole::ADMIN->value]);
    }

    #[test]
    public function admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $response = $this->delete(route('admins.destroy', $admin->id));

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}