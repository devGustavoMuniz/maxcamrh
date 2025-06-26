<?php

namespace App\Actions\Admins;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAdminAction
{
    public function execute(User $admin, array $data): bool
    {
        $admin->name = $data['name'];
        $admin->email = $data['email'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        return $admin->save();
    }
}
