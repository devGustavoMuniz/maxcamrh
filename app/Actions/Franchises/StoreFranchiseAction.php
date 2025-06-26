<?php

namespace App\Actions\Franchises;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreFranchiseAction
{
    public function execute(array $data, ?UploadedFile $documentFile): User
    {
        $documentPath = $documentFile?->store('franchise_documents', 'public');

        return DB::transaction(function () use ($data, $documentPath) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::FRANCHISE->value,
                'email_verified_at' => now(),
            ]);

            $user->franchise()->create([
                'maxcam_email' => $data['maxcam_email'],
                'cnpj' => $data['cnpj'],
                'max_client' => $data['max_client'],
                'contract_start_date' => $data['contract_start_date'],
                'actuation_region' => $data['actuation_region'],
                'document_url' => $documentPath,
                'observations' => $data['observations'],
            ]);

            return $user;
        });
    }
}
