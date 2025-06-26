<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ClientResource extends JsonResource
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
            'cnpj' => $this->cnpj,
            'test_number' => $this->test_number ?: 'N/A',
            'contract_end_date' => $this->contract_end_date?->format('d/m/Y'),
            'contract_end_date_form' => $this->contract_end_date?->format('Y-m-d'),
            'is_monthly_contract' => (bool) $this->is_monthly_contract,
            'phone' => $this->phone ?: 'N/A',
            'logo_url' => $this->logo_url,
            'logo_full_url' => $this->logo_url ? Storage::disk('public')->url($this->logo_url) : null,
            'franchise_id' => $this->franchise_id,
            'franchise_name' => $this->whenLoaded('franchise', fn() => $this->franchise?->user?->name ?? 'N/A'),
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s'),

            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
