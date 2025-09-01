<?php

namespace App\Actions\Collaborators;

use App\Models\Collaborator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use Throwable;

class UpdateCollaboratorAction
{
    /**
     * @throws Throwable
     */
    public function execute(Collaborator $collaborator, Request $request): Collaborator
    {
        $validated = $request->validated();
        $loggedInUser = $request->user();

        $userData = $validated['user'];
        $collaboratorData = $validated['collaborator'];
        $addressData = $validated['address'] ?? null;

        $collaboratorUser = $collaborator->user;

        if ($request->hasFile('collaborator.photo_file')) {
            if ($collaborator->photo_url) { Storage::disk('public')->delete($collaborator->photo_url); }
            $collaboratorData['photo_url'] = $request->file('collaborator.photo_file')->store('collaborator_photos', 'public');
        }
        if ($request->hasFile('collaborator.curriculum_file')) {
            if ($collaborator->curriculum_url) { Storage::disk('public')->delete($collaborator->curriculum_url); }
            $collaboratorData['curriculum_url'] = $request->file('collaborator.curriculum_file')->store('collaborator_curriculums', 'public');
        }

        DB::transaction(function () use ($userData, $collaboratorData, $addressData, $collaboratorUser, $collaborator, $loggedInUser) {
            if (!empty($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            } else {
                unset($userData['password']);
            }
            $collaboratorUser->update($userData);

            if ($loggedInUser->role !== UserRole::ADMIN && $loggedInUser->role !== UserRole::FRANCHISE) {
                unset($collaboratorData['client_id']);
            }
            $collaborator->update($collaboratorData);

            if ($addressData && !empty(array_filter($addressData))) {
                $collaboratorUser->address()->updateOrCreate([], $addressData);
            }
        });

        return $collaborator->fresh();
    }
}
