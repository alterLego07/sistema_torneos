<?php

namespace App\Domains\Tournaments\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class Category extends Model
{

  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = [
    'discipline_id',
    'name',
    'format',
    'team_size',
    'min_players',
    'max_players',
    'rules'
  ];
  protected $casts = ['rules' => 'array'];

  public function discipline(): BelongsTo
  {
    return $this->belongsTo(Discipline::class);
  }
  public function registrations(): HasMany
  {
    return $this->hasMany(\App\Domains\Registration\Domain\Models\CategoryRegistration::class);
  }
  public function matches(): HasMany
  {
    return $this->hasMany(\App\Domains\Competition\Domain\Models\MatchModel::class, 'category_id');
  }
}
