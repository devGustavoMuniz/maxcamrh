<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasUuids;

  protected $fillable = [
    'name',
    'email',
    'password',
    'role',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'role' => UserRole::class,
    ];
  }

  public function addresses(): HasMany
  {
    return $this->hasMany(Address::class);
  }
}
