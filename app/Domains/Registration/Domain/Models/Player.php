<?php

namespace App\Domains\Registration\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $fillable = ['first_name', 'last_name', 'document', 'birthdate', 'gender', 'phone', 'email'];
  protected $casts = ['birthdate' => 'date'];

  public function participants(): BelongsToMany
  {
    return $this->belongsToMany(Participant::class)->withPivot(['role'])->withTimestamps();
  }
}
