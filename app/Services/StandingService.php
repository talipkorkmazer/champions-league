<?php

namespace App\Services;

use App\Models\League;
use App\Models\Team;
use App\Services\Interfaces\StandingServiceInterface;
use App\DTOs\TeamStatsDTO;

/**
 * Service class for calculating and managing league standings
 */
class StandingService implements StandingServiceInterface
{
    /**
     * Calculate standings for a league
     *
     * @param League $league The league to calculate standings for
     * @return array Array of team standings sorted by position
     */
    public function calculateStandings(League $league): array
    {
        $standings = [];

        foreach ($league->teams as $team) {
            $teamStats = $this->calculateTeamStats($team, $league);
            $standings[] = $teamStats->toArray();
        }

        return $this->sortStandings($standings);
    }

    /**
     * Calculate team statistics for a specific team in a league
     *
     * @param Team $team The team to calculate statistics for
     * @param League $league The league to calculate statistics for
     * @return TeamStatsDTO The calculated team statistics
     */
    public function calculateTeamStats(Team $team, League $league): TeamStatsDTO
    {
        $teamStats = new TeamStatsDTO($team);

        $this->processTeamMatches($team, $league->id, $teamStats);

        $teamStats->calculatePoints(config('league.points_for_win'), config('league.points_for_draw'));
        $teamStats->calculateGoalDifference();

        return $teamStats;
    }

    /**
     * Sort standings by points, goal difference, and goals scored
     *
     * @param array $standings Array of team standings to sort
     * @return array Sorted array of team standings
     */
    public function sortStandings(array $standings): array
    {
        usort(array: $standings, callback: function (array $a, array $b): int {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });

        return $standings;
    }

    /**
     * Process all matches for a team in a specific league
     *
     * @param Team $team The team to process matches for
     * @param int $leagueId The ID of the league
     * @param TeamStatsDTO $teamStats The team stats DTO to update
     * @return void
     */
    private function processTeamMatches(Team $team, int $leagueId, TeamStatsDTO $teamStats): void
    {
        foreach ($team->matchesAsHome as $match) {
            if ($match->league_id === $leagueId && $match->is_played) {
                $teamStats->addMatchResult($match->home_score, $match->away_score, true);
            }
        }

        foreach ($team->matchesAsAway as $match) {
            if ($match->league_id === $leagueId && $match->is_played) {
                $teamStats->addMatchResult($match->away_score, $match->home_score, false);
            }
        }
    }
}
