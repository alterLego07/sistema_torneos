<?php

namespace App\Domains\Registration\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Participant extends Model
{
  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = ['type', 'name', 'metadata'];
  protected $casts = ['metadata' => 'array'];

  public function players(): BelongsToMany
  {
    return $this->belongsToMany(Player::class)->withPivot(['role'])->withTimestamps();
  }
}
