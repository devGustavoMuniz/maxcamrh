<?php

namespace App\Models;

use Database\Factories\FranchiseFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property string $id
 * @property string $user_id
 * @property string $maxcam_email
 * @property string $cnpj
 * @property int $max_client
 * @property Carbon $contract_start_date
 * @property string $actuation_region
 * @property string $document_url
 * @property string $observations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read User $user
 * @method static FranchiseFactory factory($count = null, $state = [])
 * @method static Builder<static>|Franchise newModelQuery()
 * @method static Builder<static>|Franchise newQuery()
 * @method static Builder<static>|Franchise query()
 * @method static Builder<static>|Franchise whereActuationRegion($value)
 * @method static Builder<static>|Franchise whereCnpj($value)
 * @method static Builder<static>|Franchise whereContractStartDate($value)
 * @method static Builder<static>|Franchise whereCreatedAt($value)
 * @method static Builder<static>|Franchise whereDocumentUrl($value)
 * @method static Builder<static>|Franchise whereId($value)
 * @method static Builder<static>|Franchise whereMaxClient($value)
 * @method static Builder<static>|Franchise whereMaxcamEmail($value)
 * @method static Builder<static>|Franchise whereObservations($value)
 * @method static Builder<static>|Franchise whereUpdatedAt($value)
 * @method static Builder<static>|Franchise whereUserId($value)
 * @method static Builder<static>|Franchise withFilters(array $filters)
 * @mixin Eloquent
 */
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
