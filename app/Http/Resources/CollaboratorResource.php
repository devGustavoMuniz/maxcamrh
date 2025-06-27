<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CollaboratorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainAddress = $this->whenLoaded('user', fn() => $this->user->addresses?->latest()->first());

        return [
            'id' => $this->id,
            'photo_url' => $this->photo_url,
            'photo_full_url' => $this->photo_url ? Storage::disk('public')->url($this->photo_url) : null,
            'curriculum_url' => $this->curriculum_url,
            'curriculum_full_url' => $this->curriculum_url ? Storage::disk('public')->url($this->curriculum_url) : null,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'is_special_needs_person' => (bool) $this->is_special_needs_person,
            'marital_status' => $this->marital_status,
            'scholarity' => $this->scholarity,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'nationality' => $this->nationality,
            'personal_email' => $this->personal_email,
            'business_email' => $this->business_email,
            'phone' => $this->phone,
            'cellphone' => $this->cellphone,
            'emergency_phone' => $this->emergency_phone,
            'department' => $this->department,
            'position' => $this->position,
            'type_of_contract' => $this->type_of_contract,
            'salary' => $this->salary ? number_format($this->salary, 2, ',', '') : null,
            'admission_date' => $this->admission_date?->format('Y-m-d'),
            'direct_superior_name' => $this->direct_superior_name,
            'hierarchical_degree' => $this->hierarchical_degree,
            'observations' => $this->observations,
            'contract_start_date' => $this->contract_start_date?->format('Y-m-d'),
            'contract_expiration' => $this->contract_expiration?->format('Y-m-d'),
            'cpf' => $this->cpf,
            'rg' => $this->rg,
            'cnh' => $this->cnh,
            'reservista' => $this->reservista,
            'titulo_eleitor' => $this->titulo_eleitor,
            'zona_eleitoral' => $this->zona_eleitoral,
            'pis_ctps_numero' => $this->pis_ctps_numero,
            'ctps_serie' => $this->ctps_serie,
            'banco' => $this->banco,
            'agencia' => $this->agencia,
            'conta_corrente' => $this->conta_corrente,
            'client_id' => $this->client_id,

            'client_name' => $this->whenLoaded('client', fn() => $this->client?->user?->name ?? 'N/A'),

            'user' => new UserResource($this->whenLoaded('user')),
            'address' => new AddressResource($mainAddress),
        ];
    }
}
