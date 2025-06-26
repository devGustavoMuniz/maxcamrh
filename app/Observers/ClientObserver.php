<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class ClientObserver
{
    /**
     * Handle the Client "deleting" event.
     */
    public function deleting(Client $client): void
    {
        if ($client->logo_url) {
            Storage::disk('public')->delete($client->logo_url);
        }

        $client->user?->delete();
    }
}
