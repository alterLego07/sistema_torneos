<?php

namespace App\Domains\Tournaments\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = ['name', 'season', 'starts_on', 'ends_on', 'status'];

  protected $casts = [
    'starts_on' => 'date',
    'ends_on'   => 'date',
  ];

  public function disciplines(): HasMany
  {
    return $this->hasMany(Discipline::class);
  }
}
