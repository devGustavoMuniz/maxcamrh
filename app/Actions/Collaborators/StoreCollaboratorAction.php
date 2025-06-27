<?php

namespace App\Actions\Collaborators;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class StoreCollaboratorAction
{
    /**
     * @throws Throwable
     */
    public function execute(Request $request): User
    {
        $validated = $request->validated();
        $loggedInUser = $request->user();

        $userData = $validated['user'];
        $collaboratorData = $validated['collaborator'];
        $addressData = $validated['address'] ?? null;

        if ($loggedInUser->role === UserRole::CLIENT) {
            $collaboratorData['client_id'] = $loggedInUser->client_id;
        } else {
            $collaboratorData['client_id'] = $collaboratorData['client_id'] ?? null;
        }

        if ($request->hasFile('collaborator.photo_file')) {
            $collaboratorData['photo_url'] = $request->file('collaborator.photo_file')->store('collaborator_photos', 'public');
        }
        if ($request->hasFile('collaborator.curriculum_file')) {
            $collaboratorData['curriculum_url'] = $request->file('collaborator.curriculum_file')->store('collaborator_curriculums', 'public');
        }

        return DB::transaction(function () use ($userData, $collaboratorData, $addressData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => UserRole::COLLABORATOR->value,
                'email_verified_at' => now(),
            ]);

            $user->collaborator()->create($collaboratorData);

            if ($addressData && !empty(array_filter($addressData))) {
                $user->address()->create($addressData);
            }

            return $user;
        });
    }
}
