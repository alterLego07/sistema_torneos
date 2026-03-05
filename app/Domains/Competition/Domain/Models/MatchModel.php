<?php

namespace App\Domains\Competition\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class MatchModel extends Model
{

  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';

  protected $table = 'matches';

  protected $fillable = [
    'category_id',
    'round',
    'match_number',
    'home_participant_id',
    'away_participant_id',
    'scheduled_at',
    'venue',
    'status'
  ];

  protected $casts = ['scheduled_at' => 'datetime'];

  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Domains\Tournaments\Domain\Models\Category::class);
  }

  public function home(): BelongsTo
  {
    return $this->belongsTo(\App\Domains\Registration\Domain\Models\Participant::class, 'home_participant_id');
  }
  public function away(): BelongsTo
  {
    return $this->belongsTo(\App\Domains\Registration\Domain\Models\Participant::class, 'away_participant_id');
  }

  public function result(): HasOne
  {
    return $this->hasOne(MatchResult::class, 'match_id');
  }
}

class MatchResult extends Model
{
  use HasUlids;

  public $incrementing = false;
  protected $keyType = 'string';


  protected $fillable = ['match_id', 'home_score', 'away_score', 'details', 'registered_at'];
  protected $casts = ['details' => 'array', 'registered_at' => 'datetime'];

  public function match(): BelongsTo
  {
    return $this->belongsTo(MatchModel::class, 'match_id');
  }
}
