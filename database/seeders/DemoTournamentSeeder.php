<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Domains\Tournaments\Domain\Models\Tournament;
use App\Domains\Tournaments\Domain\Models\Discipline;
use App\Domains\Tournaments\Domain\Models\Category;

use App\Domains\Registration\Domain\Models\Player;
use App\Domains\Registration\Domain\Models\Participant;
use App\Domains\Registration\Domain\Models\CategoryRegistration;

use App\Domains\Competition\Domain\Models\MatchModel;
use App\Domains\Competition\Domain\Models\MatchResult;

use App\Domains\Competition\Application\Services\GenerateLeagueFixtureService;

class DemoTournamentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) Torneo
            $tournament = Tournament::create([
                'name' => 'Copa Chacomer 2026',
                'season' => '2026',
                'starts_on' => now()->startOfMonth()->toDateString(),
                'ends_on' => now()->addMonths(2)->endOfMonth()->toDateString(),
                'status' => 'published',
            ]);

            // 2) Disciplinas
            $football = Discipline::create([
                'tournament_id' => $tournament->id,
                'name' => 'Football',
                'config' => ['sport' => 'football'],
            ]);

            $padel = Discipline::create([
                'tournament_id' => $tournament->id,
                'name' => 'Padel',
                'config' => ['sport' => 'padel'],
            ]);

            // 3) Categorías
            $catFootballM = Category::create([
                'discipline_id' => $football->id,
                'name' => 'Masculino',
                'format' => 'league',
                'team_size' => 6,
                'min_players' => 6,
                'max_players' => 11,
                'rules' => [
                    'points_win' => 3,
                    'points_draw' => 1,
                    'points_loss' => 0,
                    'tiebreakers' => ['points', 'goal_diff', 'goals_for'],
                ],
            ]);

            $catFootballF = Category::create([
                'discipline_id' => $football->id,
                'name' => 'Femenino',
                'format' => 'league',
                'team_size' => 6,
                'min_players' => 6,
                'max_players' => 11,
                'rules' => [
                    'points_win' => 3,
                    'points_draw' => 1,
                    'points_loss' => 0,
                    'tiebreakers' => ['points', 'goal_diff', 'goals_for'],
                ],
            ]);

            $catPadel1 = Category::create([
                'discipline_id' => $padel->id,
                'name' => 'Primera',
                'format' => 'knockout',
                'team_size' => 2,
                'min_players' => 2,
                'max_players' => 2,
                'rules' => [
                    'best_of_sets' => 3,
                    'set_games' => 6,
                    'tiebreak' => true,
                ],
            ]);

            $catPadelMix = Category::create([
                'discipline_id' => $padel->id,
                'name' => 'Mixto',
                'format' => 'knockout',
                'team_size' => 2,
                'min_players' => 2,
                'max_players' => 2,
                'rules' => [
                    'best_of_sets' => 3,
                    'set_games' => 6,
                    'tiebreak' => true,
                ],
            ]);

            // 4) Crear Players + Participants + Registrations
            // Football Masculino: 6 equipos
            $maleTeams = $this->createFootballTeams('M', [
                'Chacomer FC',
                'Automotores United',
                'Repuestos Club',
                'Logística SC',
                'Taller Racing',
                'Ventas City',
            ]);

            $this->registerParticipants($catFootballM->id, $maleTeams);

            // Football Femenino: 4 equipos
            $femaleTeams = $this->createFootballTeams('F', [
                'Chacomer Queens',
                'Automotores Ladies',
                'Repuestos Girls',
                'Logística Women',
            ]);

            $this->registerParticipants($catFootballF->id, $femaleTeams);

            // Pádel Primera: 8 parejas (perfecto para bracket)
            $padelPairs1 = $this->createPadelPairs('Primera', [
                'Gómez / Rojas',
                'Ferreira / López',
                'Martínez / Díaz',
                'Benítez / Vera',
                'Cáceres / Acosta',
                'Ramírez / Pereira',
                'Sosa / Giménez',
                'Torres / Franco',
            ]);

            $this->registerParticipants($catPadel1->id, $padelPairs1);

            // Pádel Mixto: 6 parejas (lo armamos igual, con seeds y bracket “rápido”)
            $padelPairsMix = $this->createPadelPairs('Mixto', [
                'Ana / Luis',
                'Majo / Nico',
                'Sofi / Juan',
                'Dani / Leo',
                'Paula / Marco',
                'Luz / Pedro',
            ], mixed: true);

            $this->registerParticipants($catPadelMix->id, $padelPairsMix);

            // 5) Generar partidos
            // 5.1 Liga: Football M y F
            $fixtureService = app(GenerateLeagueFixtureService::class);

            $maleIds = CategoryRegistration::where('category_id', $catFootballM->id)->pluck('participant_id')->all();
            $femaleIds = CategoryRegistration::where('category_id', $catFootballF->id)->pluck('participant_id')->all();

            $fixtureService->generate(
                categoryId: $catFootballM->id,
                participantIds: $maleIds,
                startAt: now()->addDays(1)->setTime(20, 0),
                daysBetweenRounds: 7,
                venues: ['Cancha Central', 'Cancha 2']
            );

            $fixtureService->generate(
                categoryId: $catFootballF->id,
                participantIds: $femaleIds,
                startAt: now()->addDays(2)->setTime(19, 0),
                daysBetweenRounds: 7,
                venues: ['Cancha Central']
            );

            // 5.2 Knockout: Pádel Primera y Mixto (primera ronda por seed)
            $this->generateKnockoutRound1($catPadel1->id, now()->addDays(3)->setTime(18, 0), 'Pista 1');
            $this->generateKnockoutRound1($catPadelMix->id, now()->addDays(3)->setTime(19, 30), 'Pista 2');

            // 6) Cargar algunos resultados demo (para que veas tablas y todo)
            $this->seedSomeResults($catFootballM->id, limit: 4);
            $this->seedSomeResults($catFootballF->id, limit: 2);
            $this->seedSomePadelResults($catPadel1->id, limit: 2);
        });
    }

    private function createFootballTeams(string $gender, array $teamNames): array
    {
        $teams = [];

        foreach ($teamNames as $teamName) {
            $team = Participant::create([
                'type' => 'team',
                'name' => $teamName,
                'metadata' => ['sport' => 'football', 'gender' => $gender],
            ]);

            // crear 16 jugadores por equipo (demo)
            for ($i = 1; $i <= 16; $i++) {
                $player = Player::create([
                    'first_name' => $gender === 'F' ? "Jugadora{$i}" : "Jugador{$i}",
                    'last_name' => preg_replace('/\s+/', '', $teamName),
                    'gender' => $gender,
                    'email' => null,
                    'phone' => null,
                ]);

                $team->players()->attach($player->id, [
                    'role' => $i === 1 ? 'captain' : 'player',
                ]);
            }

            $teams[] = $team;
        }

        return $teams;
    }

    private function createPadelPairs(string $label, array $pairNames, bool $mixed = false): array
    {
        $pairs = [];

        foreach ($pairNames as $name) {
            $pair = Participant::create([
                'type' => 'pair',
                'name' => $name,
                'metadata' => ['sport' => 'padel', 'category_label' => $label, 'mixed' => $mixed],
            ]);

            // Crear 2 jugadores por pareja
            [$p1, $p2] = array_map('trim', explode('/', $name . ' / ')); // fallback
            $p1 = trim($p1);
            $p2 = trim($p2);

            $player1 = Player::create([
                'first_name' => $p1 ?: 'Player1',
                'last_name' => "Padel{$label}",
                'gender' => $mixed ? 'X' : 'M',
            ]);

            $player2 = Player::create([
                'first_name' => $p2 ?: 'Player2',
                'last_name' => "Padel{$label}",
                'gender' => $mixed ? 'X' : 'M',
            ]);

            $pair->players()->attach($player1->id, ['role' => 'player']);
            $pair->players()->attach($player2->id, ['role' => 'player']);

            $pairs[] = $pair;
        }

        return $pairs;
    }

    private function registerParticipants(string $categoryId, array $participants): void
    {
        $seed = 1;

        foreach ($participants as $p) {
            CategoryRegistration::create([
                'category_id' => $categoryId,
                'participant_id' => $p->id,
                'status' => 'active',
                'seed' => $seed,
                'points_adjustment' => 0,
            ]);

            $seed++;
        }
    }

    private function generateKnockoutRound1(string $categoryId, Carbon $startAt, string $venue): void
    {
        $regs = CategoryRegistration::where('category_id', $categoryId)
            ->orderByRaw('seed asc nulls last')
            ->get();

        $ids = $regs->pluck('participant_id')->values()->all();
        $n = count($ids);

        if ($n < 2) return;

        // Emparejar: 1 vs last, 2 vs last-1, etc.
        $pairs = [];
        for ($i = 0; $i < intdiv($n, 2); $i++) {
            $pairs[] = [$ids[$i], $ids[$n - 1 - $i]];
        }

        $matchNumber = 1;
        foreach ($pairs as [$home, $away]) {
            MatchModel::create([
                'category_id' => $categoryId,
                'round' => 1,
                'match_number' => $matchNumber,
                'home_participant_id' => $home,
                'away_participant_id' => $away,
                'scheduled_at' => $startAt->copy()->addMinutes(70 * ($matchNumber - 1)),
                'venue' => $venue,
                'status' => 'scheduled',
            ]);

            $matchNumber++;
        }
    }

    private function seedSomeResults(string $categoryId, int $limit = 3): void
    {
        $matches = MatchModel::where('category_id', $categoryId)
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();

        foreach ($matches as $m) {
            $home = rand(0, 4);
            $away = rand(0, 4);

            $m->update(['status' => 'played']);

            MatchResult::create([
                'match_id' => $m->id,
                'home_score' => $home,
                'away_score' => $away,
                'details' => [
                    'type' => 'football',
                    'notes' => 'Resultado demo seed',
                ],
                'registered_at' => now(),
            ]);
        }
    }

    private function seedSomePadelResults(string $categoryId, int $limit = 2): void
    {
        $matches = MatchModel::where('category_id', $categoryId)
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();

        foreach ($matches as $m) {
            // sets demo (best of 3)
            $sets = [
                ['home' => 6, 'away' => 4],
                ['home' => 3, 'away' => 6],
                ['home' => 6, 'away' => 2],
            ];

            $homeSets = 2;
            $awaySets = 1;

            $m->update(['status' => 'played']);

            MatchResult::create([
                'match_id' => $m->id,
                'home_score' => $homeSets, // sets ganados
                'away_score' => $awaySets,
                'details' => [
                    'type' => 'padel',
                    'sets' => $sets,
                    'notes' => 'Resultado demo seed',
                ],
                'registered_at' => now(),
            ]);
        }
    }
}
