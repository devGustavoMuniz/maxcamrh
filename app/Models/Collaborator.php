<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collaborator extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'user_id',
    'photo_url',
    'curriculum_url',
    'date_of_birth',
    'gender',
    'is_special_needs_person',
    'marital_status',
    'scholarity',
    'father_name',
    'mother_name',
    'nationality',
    'personal_email',
    'business_email',
    'phone',
    'cellphone',
    'emergency_phone',
    'department',
    'position',
    'type_of_contract',
    'salary',
    'admission_date',
    'direct_superior_name',
    'hierarchical_degree',
    'observations',
    'contract_start_date',
    'contract_expiration',
    'cpf',
    'rg',
    'cnh',
    'reservista',
    'titulo_eleitor',
    'zona_eleitoral',
    'pis_ctps_numero',
    'ctps_serie',
    'banco',
    'agencia',
    'conta_corrente',
    'client_id'
  ];

  protected $casts = [
    'is_special_needs_person' => 'boolean',
    'date_of_birth' => 'date:Y-m-d',
    'admission_date' => 'date:Y-m-d',
    'contract_start_date' => 'date:Y-m-d',
    'contract_expiration' => 'date:Y-m-d',
    'salary' => 'decimal:2',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function client(): BelongsTo
  {
    return $this->belongsTo(Client::class);
  }

    /**
     * Aplica filtros de busca e de perfil de usuário à query de colaboradores.
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

        $query->when($filters['client_id'] ?? null, function (Builder $q, $clientId) {
            $q->where('client_id', $clientId);
        });

        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) return;

        if ($user->role === UserRole::FRANCHISE) {
            if ($user->franchise) {
                $query->whereHas('client', function (Builder $clientQuery) use ($user) {
                    $clientQuery->where('franchise_id', $user->franchise->id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->role === UserRole::CLIENT) {
            if ($user->client) {
                $query->where('client_id', $user->client->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
    }
}
