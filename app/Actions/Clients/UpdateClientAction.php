<?php

namespace App\Actions\Clients;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateClientAction
{
    public function execute(Client $client, array $data, ?UploadedFile $logoFile, User $loggedInUser): bool
    {
        $logoPath = $client->logo_url;
        if ($logoFile) {
            if ($client->logo_url) {
                Storage::disk('public')->delete($client->logo_url);
            }
            $logoPath = $logoFile->store('client_logos', 'public');
        }

        return DB::transaction(function () use ($client, $data, $logoPath, $loggedInUser) {
            /** @var User $clientUser */
            $clientUser = $client->user;

            $clientUser->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? Hash::make($data['password']) : $clientUser->password,
            ]);

            $clientDataToUpdate = [
                'cnpj' => $data['cnpj'],
                'test_number' => $data['test_number'],
                'contract_end_date' => $data['contract_end_date'],
                'is_monthly_contract' => $data['is_monthly_contract'],
                'phone' => $data['phone'],
                'logo_url' => $logoPath,
            ];

            if ($loggedInUser->role === UserRole::ADMIN) {
                if (array_key_exists('franchise_id', $data)) {
                    $clientDataToUpdate['franchise_id'] = $data['franchise_id'];
                }
            }

            return $client->update($clientDataToUpdate);
        });
    }
}
