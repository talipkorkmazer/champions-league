<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Services\Interfaces\FixtureServiceInterface;
use App\Services\DTOs\MatchDTO;

class FixtureService implements FixtureServiceInterface
{
    private const TOTAL_WEEKS = 6;
    private const MATCHES_PER_WEEK = 2;

    public function generateFixtures(League $league, array $teamIds): void
    {
        $allPossibleMatches = $this->generateAllPossibleMatches($teamIds);
        shuffle($allPossibleMatches);

        $matchesToInsert = [];
        for ($week = 1; $week <= self::TOTAL_WEEKS; $week++) {
            $matchesThisWeek = array_slice($allPossibleMatches, ($week - 1) * self::MATCHES_PER_WEEK, self::MATCHES_PER_WEEK);

            foreach ($matchesThisWeek as $matchData) {
                $match = $this->createMatch($matchData['home_team_id'], $matchData['away_team_id'], $week);
                $matchesToInsert[] = $this->prepareMatchForInsert($match, $league->id);
            }
        }

        LeagueMatch::upsert($matchesToInsert, ['league_id', 'week', 'home_team_id', 'away_team_id']);
    }

    public function generateAllPossibleMatches(array $teamIds): array
    {
        $matches = [];
        $teamCount = count($teamIds);

        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $matches[] = [
                    'home_team_id' => $teamIds[$i],
                    'away_team_id' => $teamIds[$j]
                ];
                $matches[] = [
                    'home_team_id' => $teamIds[$j],
                    'away_team_id' => $teamIds[$i]
                ];
            }
        }

        return $matches;
    }

    public function createMatch(int $homeTeamId, int $awayTeamId, int $week): MatchDTO
    {
        return new MatchDTO($homeTeamId, $awayTeamId, $week);
    }

    private function prepareMatchForInsert(MatchDTO $match, int $leagueId): array
    {
        return [
            'league_id' => $leagueId,
            'week' => $match->week,
            'home_team_id' => $match->homeTeamId,
            'away_team_id' => $match->awayTeamId,
            'is_played' => $match->isPlayed,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
