<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\ClientFactory;
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
 * @property string|null $franchise_id
 * @property string $cnpj
 * @property string $test_number
 * @property Carbon $contract_end_date
 * @property bool $is_monthly_contract
 * @property string $phone
 * @property string $logo_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Collaborator> $collaborators
 * @property-read int|null $collaborators_count
 * @property-read Franchise|null $franchise
 * @property-read User $user
 * @method static ClientFactory factory($count = null, $state = [])
 * @method static Builder<static>|Client newModelQuery()
 * @method static Builder<static>|Client newQuery()
 * @method static Builder<static>|Client query()
 * @method static Builder<static>|Client whereCnpj($value)
 * @method static Builder<static>|Client whereContractEndDate($value)
 * @method static Builder<static>|Client whereCreatedAt($value)
 * @method static Builder<static>|Client whereFranchiseId($value)
 * @method static Builder<static>|Client whereId($value)
 * @method static Builder<static>|Client whereIsMonthlyContract($value)
 * @method static Builder<static>|Client whereLogoUrl($value)
 * @method static Builder<static>|Client wherePhone($value)
 * @method static Builder<static>|Client whereTestNumber($value)
 * @method static Builder<static>|Client whereUpdatedAt($value)
 * @method static Builder<static>|Client whereUserId($value)
 * @method static Builder<static>|Client withFilters(array $filters)
 * @mixin Eloquent
 */
class Client extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'user_id',
    'cnpj',
    'test_number',
    'contract_end_date',
    'is_monthly_contract',
    'phone',
    'logo_url',
    'franchise_id'
  ];

  protected $casts = [
    'contract_end_date' => 'date',
    'is_monthly_contract' => 'boolean',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function franchise(): BelongsTo
  {
    return $this->belongsTo(Franchise::class);
  }

  public function collaborators() : HasMany
  {
    return $this->hasMany(Collaborator::class);
  }

    /**
     * Aplica filtros de busca e de perfil de usuário à query de clientes.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeWithFilters(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $q, $search) {
            $q->whereHas('user', function (Builder $userQuery) use ($search) {
                $lowerSearchTerm = strtolower($search);
                $userQuery->whereRaw('LOWER(name) LIKE ?', ["%{$lowerSearchTerm}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$lowerSearchTerm}%"]);
            });
        });

        $query->when($filters['franchise_id'] ?? null, function (Builder $q, $franchiseId) {
            $q->where('franchise_id', $franchiseId);
        });

        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            return;
        }

        if ($user->role === UserRole::FRANCHISE) {
            $query->where('franchise_id', $user->franchise?->id);
        }

        if ($user->role === UserRole::CLIENT) {
            $query->where('id', $user->client_id);
        }
    }
}
