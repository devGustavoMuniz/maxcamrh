<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Franchise extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'user_id',
    'maxcam_email',
    'cnpj',
    'max_client',
    'contract_start_date',
    'actuation_region',
    'document_url',
    'observations',
  ];

  protected $casts = [
    'contract_start_date' => 'date',
    'max_client' => 'integer',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function clients(): HasMany
  {
    return $this->hasMany(Client::class);
  }

    /**
     * Aplica filtros de busca à query de franqueados.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeWithFilters(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $q, $search) {
            $q->where(function (Builder $innerQuery) use ($search) {
                $innerQuery->where('cnpj', 'like', "%{$search}%")
                    ->orWhere('maxcam_email', 'like', "%{$search}%")
                    ->orWhere('actuation_region', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        });
    }
}
