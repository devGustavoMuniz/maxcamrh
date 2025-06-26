<?php

namespace App\Actions\Admins;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StoreAdminAction
{
    public function execute(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);
    }
}
