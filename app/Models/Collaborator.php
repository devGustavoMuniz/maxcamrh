<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\CollaboratorFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property string $id
 * @property string $user_id
 * @property string|null $client_id
 * @property string|null $photo_url
 * @property string|null $curriculum_url
 * @property Carbon|null $date_of_birth
 * @property string|null $gender
 * @property bool $is_special_needs_person
 * @property string|null $marital_status
 * @property string|null $scholarity
 * @property string|null $father_name
 * @property string|null $mother_name
 * @property string|null $nationality
 * @property string|null $personal_email
 * @property string|null $business_email
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $emergency_phone
 * @property string|null $department
 * @property string|null $position
 * @property string|null $type_of_contract
 * @property numeric|null $salary
 * @property Carbon|null $admission_date
 * @property string|null $direct_superior_name
 * @property string|null $hierarchical_degree
 * @property string|null $observations
 * @property Carbon|null $contract_start_date
 * @property Carbon|null $contract_expiration
 * @property string|null $cpf
 * @property string|null $rg
 * @property string|null $cnh
 * @property string|null $reservista
 * @property string|null $titulo_eleitor
 * @property string|null $zona_eleitoral
 * @property string|null $pis_ctps_numero
 * @property string|null $ctps_serie
 * @property string|null $banco
 * @property string|null $agencia
 * @property string|null $conta_corrente
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Client|null $client
 * @property-read User $user
 * @method static CollaboratorFactory factory($count = null, $state = [])
 * @method static Builder<static>|Collaborator newModelQuery()
 * @method static Builder<static>|Collaborator newQuery()
 * @method static Builder<static>|Collaborator query()
 * @method static Builder<static>|Collaborator whereAdmissionDate($value)
 * @method static Builder<static>|Collaborator whereAgencia($value)
 * @method static Builder<static>|Collaborator whereBanco($value)
 * @method static Builder<static>|Collaborator whereBusinessEmail($value)
 * @method static Builder<static>|Collaborator whereCellphone($value)
 * @method static Builder<static>|Collaborator whereClientId($value)
 * @method static Builder<static>|Collaborator whereCnh($value)
 * @method static Builder<static>|Collaborator whereContaCorrente($value)
 * @method static Builder<static>|Collaborator whereContractExpiration($value)
 * @method static Builder<static>|Collaborator whereContractStartDate($value)
 * @method static Builder<static>|Collaborator whereCpf($value)
 * @method static Builder<static>|Collaborator whereCreatedAt($value)
 * @method static Builder<static>|Collaborator whereCtpsSerie($value)
 * @method static Builder<static>|Collaborator whereCurriculumUrl($value)
 * @method static Builder<static>|Collaborator whereDateOfBirth($value)
 * @method static Builder<static>|Collaborator whereDepartment($value)
 * @method static Builder<static>|Collaborator whereDirectSuperiorName($value)
 * @method static Builder<static>|Collaborator whereEmergencyPhone($value)
 * @method static Builder<static>|Collaborator whereFatherName($value)
 * @method static Builder<static>|Collaborator whereGender($value)
 * @method static Builder<static>|Collaborator whereHierarchicalDegree($value)
 * @method static Builder<static>|Collaborator whereId($value)
 * @method static Builder<static>|Collaborator whereIsSpecialNeedsPerson($value)
 * @method static Builder<static>|Collaborator whereMaritalStatus($value)
 * @method static Builder<static>|Collaborator whereMotherName($value)
 * @method static Builder<static>|Collaborator whereNationality($value)
 * @method static Builder<static>|Collaborator whereObservations($value)
 * @method static Builder<static>|Collaborator wherePersonalEmail($value)
 * @method static Builder<static>|Collaborator wherePhone($value)
 * @method static Builder<static>|Collaborator wherePhotoUrl($value)
 * @method static Builder<static>|Collaborator wherePisCtpsNumero($value)
 * @method static Builder<static>|Collaborator wherePosition($value)
 * @method static Builder<static>|Collaborator whereReservista($value)
 * @method static Builder<static>|Collaborator whereRg($value)
 * @method static Builder<static>|Collaborator whereSalary($value)
 * @method static Builder<static>|Collaborator whereScholarity($value)
 * @method static Builder<static>|Collaborator whereTituloEleitor($value)
 * @method static Builder<static>|Collaborator whereTypeOfContract($value)
 * @method static Builder<static>|Collaborator whereUpdatedAt($value)
 * @method static Builder<static>|Collaborator whereUserId($value)
 * @method static Builder<static>|Collaborator whereZonaEleitoral($value)
 * @method static Builder<static>|Collaborator withFilters(array $filters)
 * @mixin Eloquent
 */
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
                $userQuery->whereRaw('LOWER(name) LIKE ?', ["%$lowerSearchTerm%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%$lowerSearchTerm%"]);
            });
        });

        $query->when($filters['client_id'] ?? null, function (Builder $q, $clientId) {
            $q->where('client_id', $clientId);
        });

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
        }
        if ($user->role === UserRole::CLIENT) {
            if ($user->client) {
                $query->where('client_id', $user->client->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
    }
}
