<?php

namespace App\Domains\Tournaments\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class Discipline extends Model
{
  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = ['tournament_id', 'name', 'config'];
  protected $casts = ['config' => 'array'];

  public function tournament(): BelongsTo
  {
    return $this->belongsTo(Tournament::class);
  }
  public function categories(): HasMany
  {
    return $this->hasMany(Category::class);
  }
}
