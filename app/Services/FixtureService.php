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
        $week = 1;

        while (!empty($allPossibleMatches) && $week <= config('league.total_weeks')) {
            $matchesThisWeek = [];
            $teamsScheduledThisWeek = [];

            foreach ($allPossibleMatches as $key => $matchData) {
                $home = $matchData['home_team_id'];
                $away = $matchData['away_team_id'];

                if (!in_array($home, $teamsScheduledThisWeek) && !in_array($away, $teamsScheduledThisWeek)) {
                    $match = $this->createMatch($home, $away, $week);
                    $matchesThisWeek[] = $this->prepareMatchForInsert($match, $league->id);

                    // Mark these teams as busy for this week
                    $teamsScheduledThisWeek[] = $home;
                    $teamsScheduledThisWeek[] = $away;

                    // Remove match from pool
                    unset($allPossibleMatches[$key]);
                }

                // Stop adding matches once we hit configured limit for the week
                if (count($matchesThisWeek) >= config('league.matches_per_week')) {
                    break;
                }
            }

            // Reindex array to avoid gaps after unset()
            $allPossibleMatches = array_values($allPossibleMatches);

            $matchesToInsert = array_merge($matchesToInsert, $matchesThisWeek);
            $week++;
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
