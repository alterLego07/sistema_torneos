<?php

namespace App\Domains\Registration\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class CategoryRegistration extends Model
{
  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = ['category_id', 'participant_id', 'status', 'seed', 'points_adjustment'];

  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Domains\Tournaments\Domain\Models\Category::class);
  }
  public function participant(): BelongsTo
  {
    return $this->belongsTo(Participant::class);
  }
}
