<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Colaborator extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'user_id',
    'photo_url',
    'curriculum',
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
}
