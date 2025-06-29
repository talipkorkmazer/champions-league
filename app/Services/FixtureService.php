<?php

namespace App\Services;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Services\Interfaces\FixtureServiceInterface;
use App\DTOs\MatchDTO;

/**
 * Service class for generating and managing league fixtures
 */
class FixtureService implements FixtureServiceInterface
{
    /**
     * Generate fixtures for a league
     *
     * @param League $league The league to generate fixtures for
     * @param array $teamIds Array of team IDs to include in fixtures
     * @return array Array of match data for database insertion
     */
    public function generateFixtures(League $league, array $teamIds): array
    {
        $allPossibleMatches = $this->generateAllPossibleMatches($teamIds);
        shuffle($allPossibleMatches);

        $matchesToInsert = [];
        for ($week = 1; $week <= config('league.total_weeks'); $week++) {
            $matchesThisWeek = array_slice($allPossibleMatches, ($week - 1) * config('league.matches_per_week'), config('league.matches_per_week'));

            foreach ($matchesThisWeek as $matchData) {
                $match = $this->createMatch($matchData['home_team_id'], $matchData['away_team_id'], $week);
                $matchesToInsert[] = $this->prepareMatchForInsert($match, $league->id);
            }
        }

        return $matchesToInsert;
    }

    /**
     * Generate all possible matches between teams
     *
     * @param array $teamIds Array of team IDs
     * @return array Array of all possible match combinations
     */
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

    /**
     * Create a match DTO
     *
     * @param int $homeTeamId The ID of the home team
     * @param int $awayTeamId The ID of the away team
     * @param int $week The week number for the match
     * @return MatchDTO The created match DTO
     */
    public function createMatch(int $homeTeamId, int $awayTeamId, int $week): MatchDTO
    {
        return new MatchDTO($homeTeamId, $awayTeamId, $week);
    }

    /**
     * Prepare match data for database insertion
     *
     * @param MatchDTO $match The match DTO to prepare
     * @param int $leagueId The ID of the league
     * @return array Array of match data ready for database insertion
     */
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
