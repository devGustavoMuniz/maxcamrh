<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property UserRole $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Address|null $addresses
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\Collaborator|null $collaborator
 * @property-read \App\Models\Franchise|null $franchise
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withFilters(array $filters)
 * @mixin \Eloquent
 */
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

  public function addresses(): HasOne
  {
    return $this->hasOne(Address::class);
  }

  public function franchise(): HasOne
  {
    return $this->hasOne(Franchise::class);
  }

  public function client(): HasOne
  {
    return $this->hasOne(Client::class);
  }

  public function collaborator(): HasOne
  {
    return $this->hasOne(Collaborator::class);
  }

    /**
     * Aplica filtros de busca à query de usuários.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeWithFilters(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $q, $search) {
            $q->where(function (Builder $innerQuery) use ($search) {
                $innerQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });
    }
}
