<?php

namespace App\Domains\Competition\Application\Services;

use App\Domains\Competition\Domain\Models\MatchModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GenerateLeagueFixtureService
{
  /**
   * Genera un round-robin (1 vuelta) para una lista de participant_ids.
   * Si la cantidad es impar, agrega BYE (descansa).
   *
   * @param  string       $categoryId
   * @param  array        $participantIds
   * @param  Carbon|null  $startAt
   * @param  int          $daysBetweenRounds
   * @param  array        $venues
   * @return Collection<MatchModel>
   */
  public function generate(
    string $categoryId,
    array $participantIds,
    ?Carbon $startAt = null,
    int $daysBetweenRounds = 7,
    array $venues = ['Cancha 1']
  ): Collection {
    $startAt ??= now()->startOfDay()->addDays(1)->setTime(20, 0);

    $ids = array_values($participantIds);
    $bye = null;

    if (count($ids) < 2) {
      return collect();
    }

    // Si impar, agregamos un BYE
    if (count($ids) % 2 === 1) {
      $bye = '__BYE__';
      $ids[] = $bye;
    }

    $n = count($ids);
    $rounds = $n - 1;
    $half = (int) ($n / 2);

    $list = $ids;
    $matches = collect();
    $matchNumber = 1;

    for ($round = 1; $round <= $rounds; $round++) {
      $roundAt = $startAt->copy()->addDays(($round - 1) * $daysBetweenRounds);

      $pairs = [];
      for ($i = 0; $i < $half; $i++) {
        $home = $list[$i];
        $away = $list[$n - 1 - $i];
        $pairs[] = [$home, $away];
      }

      // Alternar localía para reducir sesgo
      if ($round % 2 === 0) {
        $pairs = array_map(fn($p) => [$p[1], $p[0]], $pairs);
      }

      foreach ($pairs as [$home, $away]) {
        if ($home === $bye || $away === $bye) {
          continue; // descansa
        }

        $venue = $venues[($matchNumber - 1) % max(count($venues), 1)];

        $matches->push(
          MatchModel::create([
            'category_id' => $categoryId,
            'round' => $round,
            'match_number' => $matchNumber,
            'home_participant_id' => $home,
            'away_participant_id' => $away,
            'scheduled_at' => $roundAt->copy()->addMinutes(90 * (($matchNumber - 1) % 2)),
            'venue' => $venue,
            'status' => 'scheduled',
          ])
        );

        $matchNumber++;
      }

      // Rotación "circle method"
      // fijo el primero y rotar el resto
      $fixed = $list[0];
      $rest = array_slice($list, 1);

      $last = array_pop($rest);
      array_unshift($rest, $last);

      $list = array_merge([$fixed], $rest);
    }

    return $matches;
  }
}
