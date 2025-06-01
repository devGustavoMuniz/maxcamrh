<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
