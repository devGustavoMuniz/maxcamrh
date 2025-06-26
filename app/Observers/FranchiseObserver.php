<?php

namespace App\Observers;

use App\Models\Franchise;
use Illuminate\Support\Facades\Storage;

class FranchiseObserver
{
    /**
     * Handle the Franchise "deleting" event.
     */
    public function deleting(Franchise $franchise): void
    {
        if ($franchise->document_url) {
            Storage::disk('public')->delete($franchise->document_url);
        }

        $franchise->user?->delete();
    }
}
