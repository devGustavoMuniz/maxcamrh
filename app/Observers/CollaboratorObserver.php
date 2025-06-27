<?php

namespace App\Observers;

use App\Models\Collaborator;
use Illuminate\Support\Facades\Storage;

class CollaboratorObserver
{
    /**
     * Handle the Collaborator "deleting" event.
     */
    public function deleting(Collaborator $collaborator): void
    {
        if ($collaborator->photo_url) {
            Storage::disk('public')->delete($collaborator->photo_url);
        }

        if ($collaborator->curriculum_url) {
            Storage::disk('public')->delete($collaborator->curriculum_url);
        }

        $user = $collaborator->user;

        if ($user) {
            $user->address?->delete();
            $user->delete();
        }
    }
}
