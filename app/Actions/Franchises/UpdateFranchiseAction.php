<?php

namespace App\Actions\Franchises;

use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateFranchiseAction
{
    public function execute(Franchise $franchise, array $data, ?UploadedFile $documentFile): Franchise
    {
        $documentPath = $franchise->document_url;
        if ($documentFile) {
            if ($franchise->document_url) {
                Storage::disk('public')->delete($franchise->document_url);
            }
            $documentPath = $documentFile->store('franchise_documents', 'public');
        }

        DB::transaction(function () use ($franchise, $data, $documentPath) {
            $user = $franchise->user;

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $user->update($userData);

            $franchiseData = [
                'maxcam_email' => $data['maxcam_email'],
                'cnpj' => $data['cnpj'],
                'max_client' => $data['max_client'],
                'contract_start_date' => $data['contract_start_date'],
                'actuation_region' => $data['actuation_region'],
                'observations' => $data['observations'],
                'document_url' => $documentPath,
            ];
            $franchise->update($franchiseData);
        });

        return $franchise->fresh();
    }
}
