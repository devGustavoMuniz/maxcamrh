<?php

namespace App\Actions\Admins;

use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAdminAction
{
    public function execute(User $admin, UpdateAdminRequest $request): User
    {
        $data = $request->validated();

        $admin->name = $data['name'];
        $admin->email = $data['email'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return $admin->fresh();
    }
}
