<?php

namespace App\Models;

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
}
