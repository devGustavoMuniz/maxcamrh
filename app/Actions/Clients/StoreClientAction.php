<?php

namespace App\Actions\Clients;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class StoreClientAction
{
    /**
     * @throws Throwable
     */
    public function execute(array $data, ?UploadedFile $logoFile, User $loggedInUser): User
    {
        $logoPath = $logoFile?->store('client_logos', 'public');

        return DB::transaction(function () use ($data, $logoPath, $loggedInUser) {
            $clientUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::CLIENT->value,
                'email_verified_at' => now(),
            ]);

            $clientData = [
                'cnpj' => $data['cnpj'],
                'test_number' => $data['test_number'],
                'contract_end_date' => $data['contract_end_date'],
                'is_monthly_contract' => $data['is_monthly_contract'],
                'phone' => $data['phone'],
                'logo_url' => $logoPath,
                'franchise_id' => null,
            ];

            if ($loggedInUser->role === UserRole::ADMIN) {
                $clientData['franchise_id'] = $data['franchise_id'] ?? null;
            }

            if ($loggedInUser->role === UserRole::FRANCHISE) {
                $clientData['franchise_id'] = $loggedInUser->franchise_id;
            }

            $clientUser->client()->create($clientData);

            return $clientUser;
        });
    }
}
