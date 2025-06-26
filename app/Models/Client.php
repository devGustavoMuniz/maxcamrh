<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

        $user = auth()->user();
        if (!$user) return;

        if ($user->role === UserRole::CLIENT) {
            $query->where('id', $user->client_id);
        }
    }
}
