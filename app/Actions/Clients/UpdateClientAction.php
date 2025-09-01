<?php

namespace App\Actions\Clients;

use App\Enums\UserRole;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateClientAction
{
    /**
     * @throws Throwable
     */
    public function __invoke(Client $client, UpdateClientRequest $request): Client
    {
        $data = $request->validated();
        $logoFile = $request->file('logo_file');
        $loggedInUser = $request->user();

        $logoPath = $client->logo_url;
        if ($logoFile) {
            if ($client->logo_url) {
                Storage::disk('public')->delete($client->logo_url);
            }
            $logoPath = $logoFile->store('client_logos', 'public');
        }

        DB::transaction(function () use ($client, $data, $logoPath, $loggedInUser) {
            $clientUser = $client->user;

            $clientUser->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? Hash::make($data['password']) : $clientUser->password,
            ]);

            $clientDataToUpdate = [
                'cnpj' => $data['cnpj'],
                'test_number' => $data['test_number'] ?? null,
                'contract_end_date' => $data['contract_end_date'] ?? null,
                'is_monthly_contract' => $data['is_monthly_contract'],
                'phone' => $data['phone'] ?? null,
                'logo_url' => $logoPath,
            ];

            if ($loggedInUser->role === UserRole::ADMIN) {
                if (array_key_exists('franchise_id', $data)) {
                    $clientDataToUpdate['franchise_id'] = $data['franchise_id'];
                }
            }

            $client->update($clientDataToUpdate);
        });

        return $client->fresh();
    }
}
