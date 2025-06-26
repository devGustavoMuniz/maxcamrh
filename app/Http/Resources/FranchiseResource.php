<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FranchiseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'maxcam_email' => $this->maxcam_email,
            'cnpj' => $this->cnpj,
            'max_client' => $this->max_client,
            'contract_start_date' => $this->contract_start_date?->format('d/m/Y'),
            'contract_start_date_form' => $this->contract_start_date?->format('Y-m-d'),
            'actuation_region' => $this->actuation_region,
            'document_url' => $this->document_url,
            'document_full_url' => $this->document_url ? Storage::disk('public')->url($this->document_url) : null,
            'observations' => $this->observations,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s'),

            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
