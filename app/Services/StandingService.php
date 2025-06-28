<?php

namespace App\Services;

use App\Models\League;
use App\Models\Team;
use App\Services\Interfaces\StandingServiceInterface;
use App\Services\DTOs\TeamStatsDTO;

class StandingService implements StandingServiceInterface
{
    private const POINTS_FOR_WIN = 3;
    private const POINTS_FOR_DRAW = 1;

    public function calculateStandings(League $league): array
    {
        $standings = [];

        foreach ($league->teams as $team) {
            $teamStats = $this->calculateTeamStats($team->id, $league->id);
            $standings[] = $teamStats->toArray();
        }

        return $this->sortStandings($standings);
    }

    public function calculateTeamStats(int $teamId, int $leagueId): TeamStatsDTO
    {
        $team = Team::findOrFail($teamId);
        $teamStats = new TeamStatsDTO($team);

        $this->processTeamMatches($team, $leagueId, $teamStats);

        $teamStats->calculatePoints(self::POINTS_FOR_WIN, self::POINTS_FOR_DRAW);
        $teamStats->calculateGoalDifference();

        return $teamStats;
    }

    public function sortStandings(array $standings): array
    {
        usort($standings, function ($a, $b) {
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

    private function processTeamMatches(Team $team, int $leagueId, TeamStatsDTO $teamStats): void
    {
        // Process home matches
        foreach ($team->matchesAsHome as $match) {
            if ($match->league_id === $leagueId && $match->is_played) {
                $teamStats->addMatchResult($match->home_score, $match->away_score, true);
            }
        }

        // Process away matches
        foreach ($team->matchesAsAway as $match) {
            if ($match->league_id === $leagueId && $match->is_played) {
                $teamStats->addMatchResult($match->away_score, $match->home_score, false);
            }
        }
    }
}
