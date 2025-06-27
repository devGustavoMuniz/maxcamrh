<?php

namespace App\Models;

use Database\Factories\AddressFactory;
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
 * @property string $cep
 * @property string $street
 * @property string $number
 * @property string|null $complement
 * @property string $neighborhood
 * @property string $state
 * @property string $city
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static AddressFactory factory($count = null, $state = [])
 * @method static Builder<static>|Address newModelQuery()
 * @method static Builder<static>|Address newQuery()
 * @method static Builder<static>|Address query()
 * @method static Builder<static>|Address whereCep($value)
 * @method static Builder<static>|Address whereCity($value)
 * @method static Builder<static>|Address whereComplement($value)
 * @method static Builder<static>|Address whereCreatedAt($value)
 * @method static Builder<static>|Address whereId($value)
 * @method static Builder<static>|Address whereNeighborhood($value)
 * @method static Builder<static>|Address whereNumber($value)
 * @method static Builder<static>|Address whereState($value)
 * @method static Builder<static>|Address whereStreet($value)
 * @method static Builder<static>|Address whereUpdatedAt($value)
 * @method static Builder<static>|Address whereUserId($value)
 * @mixin Eloquent
 */
class Address extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'user_id',
    'cep',
    'street',
    'number',
    'complement',
    'neighborhood',
    'state',
    'city',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
