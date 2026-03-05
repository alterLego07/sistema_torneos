<?php

namespace App\Domains\Competition\Application\Services;

use App\Domains\Competition\Domain\Models\MatchModel;
use App\Domains\Registration\Domain\Models\CategoryRegistration;
use App\Domains\Registration\Domain\Models\Participant;
use Illuminate\Support\Collection;

class ComputeStandingsService
{
  /**
   * Calcula tabla de posiciones (liga fútbol) para una categoría.
   *
   * Output por fila:
   * - participant_id, name
   * - pj, pg, pe, pp
   * - gf, gc, dg
   * - pts
   */
  public function compute(string $categoryId): Collection
  {
    // 1) Participantes inscriptos (base de la tabla, aunque no hayan jugado)
    $registered = CategoryRegistration::query()
      ->where('category_id', $categoryId)
      ->where('status', 'active')
      ->with(['participant:id,name'])
      ->get()
      ->map(fn($r) => $r->participant)
      ->filter()
      ->keyBy('id');

    // Inicializar tabla
    $table = $registered->map(function (Participant $p) {
      return [
        'participant_id' => $p->id,
        'name' => $p->name,
        'pj' => 0,
        'pg' => 0,
        'pe' => 0,
        'pp' => 0,
        'gf' => 0,
        'gc' => 0,
        'dg' => 0,
        'pts' => 0,
      ];
    });

    if ($table->isEmpty()) {
      return collect();
    }

    // 2) Traer partidos jugados con resultado
    $matches = MatchModel::query()
      ->where('category_id', $categoryId)
      ->where('status', 'played')
      ->with('result:id,match_id,home_score,away_score')
      ->get()
      ->filter(
        fn($m) => $m->result
          && $m->result->home_score !== null
          && $m->result->away_score !== null
      );

    // 3) Aplicar cada partido a la tabla
    foreach ($matches as $match) {
      $homeId = $match->home_participant_id;
      $awayId = $match->away_participant_id;

      // Si el partido involucra a alguien no registrado (data sucia), lo ignoramos
      if (!isset($table[$homeId]) || !isset($table[$awayId])) {
        continue;
      }

      $hs = (int) $match->result->home_score;
      $as = (int) $match->result->away_score;

      // PJ
      $table[$homeId]['pj']++;
      $table[$awayId]['pj']++;

      // GF/GC
      $table[$homeId]['gf'] += $hs;
      $table[$homeId]['gc'] += $as;

      $table[$awayId]['gf'] += $as;
      $table[$awayId]['gc'] += $hs;

      // Resultado
      if ($hs > $as) {
        // Home gana
        $table[$homeId]['pg']++;
        $table[$awayId]['pp']++;

        $table[$homeId]['pts'] += 3;
      } elseif ($hs < $as) {
        // Away gana
        $table[$awayId]['pg']++;
        $table[$homeId]['pp']++;

        $table[$awayId]['pts'] += 3;
      } else {
        // Empate
        $table[$homeId]['pe']++;
        $table[$awayId]['pe']++;

        $table[$homeId]['pts'] += 1;
        $table[$awayId]['pts'] += 1;
      }
    }

    // 4) DG + ordenar
    $final = $table->map(function (array $row) {
      $row['dg'] = $row['gf'] - $row['gc'];
      return $row;
    })->values();

    return $final->sort(function (array $a, array $b) {
      // pts desc
      if ($a['pts'] !== $b['pts']) return $b['pts'] <=> $a['pts'];
      // dg desc
      if ($a['dg'] !== $b['dg']) return $b['dg'] <=> $a['dg'];
      // gf desc
      if ($a['gf'] !== $b['gf']) return $b['gf'] <=> $a['gf'];
      // name asc
      return strcmp($a['name'], $b['name']);
    })->values();
  }
}
