<?php

namespace App\Services;

use App\Models\Prediction;
use App\Services\Interfaces\PredictionServiceInterface;
use App\Models\League;
use App\Services\StandingService;
use App\Models\Team;

/**
 * Service class for calculating and managing championship predictions
 */
class PredictionService implements PredictionServiceInterface
{
    /**
     * Create a new PredictionService instance
     *
     * @param StandingService $standingService Service for calculating standings
     */
    public function __construct(
        private StandingService $standingService
    ) {
    }

    /**
     * Add predictions to standings
     *
     * @param array $standings Array of team standings
     * @param int $leagueId The ID of the league
     * @param int $currentWeek The current week number
     * @return array Array of standings with predictions added
     */
    public function addPredictionsToStandings(array $standings, int $leagueId, int $currentWeek): array
    {
        $predictions = $this->getPredictions($leagueId, $currentWeek);

        foreach ($standings as &$standing) {
            $teamId = $standing['team']->id;
            $standing['championship_percentage'] = $predictions[$teamId] ?? 0;
        }
        unset($standing);

        return $standings;
    }

    /**
     * Get predictions for a league and week
     *
     * @param int $leagueId The ID of the league
     * @param int $currentWeek The current week number
     * @return array Array of predictions indexed by team ID
     */
    public function getPredictions(int $leagueId, int $currentWeek): array
    {
        return Prediction::where('league_id', $leagueId)
            ->where('week', $currentWeek)
            ->get()
            ->keyBy('team_id')
            ->map(fn($prediction) => $prediction->percentage)
            ->toArray();
    }

    /**
     * Calculate championship percentages for teams
     *
     * @param array $standings Array of team standings
     * @param int $leagueId The ID of the league
     * @param int $currentWeek The current week number
     * @return array Array of standings with championship percentages
     */
    public function calculateChampionshipPercentages(array $standings, int $leagueId, int $currentWeek): array
    {
        $predictions = $this->getPredictions($leagueId, $currentWeek);

        $standingsWithPercentages = [];
        foreach ($standings as $standing) {
            $teamId = $standing['team']->id;
            $standing['championship_percentage'] = $predictions[$teamId] ?? 0;
            $standingsWithPercentages[] = $standing;
        }

        return $standingsWithPercentages;
    }

    /**
     * Calculate predictions for the league based on current standings
     *
     * @param League $league The league to calculate predictions for
     * @return void
     */
    public function calculatePredictions(League $league): void
    {
        if ($league->current_week < config('league.min_week_for_predictions')) {
            return;
        }

        $standings = $this->standingService->calculateStandings($league);
        $totalWeeks = config('league.total_weeks');

        if ($league->current_week >= $totalWeeks) {
            // Sort a copy of the standings using all tiebreakers: points, goal difference, goals for
            $sortedStandings = $standings;
            usort($sortedStandings, function ($a, $b) {
                if ($a['points'] !== $b['points']) {
                    return $b['points'] - $a['points'];
                }
                if ($a['goal_difference'] !== $b['goal_difference']) {
                    return $b['goal_difference'] - $a['goal_difference'];
                }
                return $b['goals_for'] - $a['goals_for'];
            });
            // Find the top team(s) after tiebreakers
            $topStanding = $sortedStandings[0];
            $topTeams = array_filter($sortedStandings, function ($standing) use ($topStanding) {
                return $standing['points'] === $topStanding['points'] &&
                       $standing['goal_difference'] === $topStanding['goal_difference'] &&
                       $standing['goals_for'] === $topStanding['goals_for'];
            });
            $topTeamIds = array_map(fn($standing) => $standing['team']->id, $topTeams);

            $predictions = [];
            foreach ($standings as $standing) {
                $teamId = $standing['team']->id;
                $predictions[$teamId] = in_array($teamId, $topTeamIds) ? 100.0 : 0.0;
            }
            foreach ($predictions as $teamId => $percentage) {
                Prediction::updateOrCreate(
                    [
                        'league_id' => $league->id,
                        'week' => $league->current_week,
                        'team_id' => $teamId,
                    ],
                    [
                        'percentage' => $percentage,
                    ]
                );
            }
            return;
        }

        $remainingWeeks = $totalWeeks - $league->current_week;
        $pointsForWin = config('league.points_for_win');


        $maxPossiblePoints = [];
        $currentPoints = [];
        foreach ($standings as $standing) {
            $teamId = $standing['team']->id;
            $currentPoints[$teamId] = $standing['points'];
            $maxPossiblePoints[$teamId] = $standing['points'] + ($remainingWeeks * $pointsForWin);
        }

        $maxCurrentPoints = max($currentPoints);

        $inRace = [];
        foreach ($standings as $standing) {
            $teamId = $standing['team']->id;
            if ($maxPossiblePoints[$teamId] >= $maxCurrentPoints) {
                $inRace[] = $teamId;
            }
        }

        if (count($inRace) === 1) {
            $predictions = [];
            foreach ($standings as $standing) {
                $teamId = $standing['team']->id;
                $predictions[$teamId] = $teamId === $inRace[0] ? 100.0 : 0.0;
            }
        } else {
            $teamData = [];
            $totalPoints = 0;
            $totalGoalsFor = 0;
            $totalGoalsAgainst = 0;
            foreach ($standings as $standing) {
                $teamId = $standing['team']->id;
                if (!in_array($teamId, $inRace)) {
                    continue;
                }
                $totalPoints += $standing['points'];
                $totalGoalsFor += $standing['goals_for'];
                $totalGoalsAgainst += $standing['goals_against'];
                $teamData[$teamId] = [
                    'points' => $standing['points'],
                    'goal_difference' => $standing['goal_difference'],
                    'goals_for' => $standing['goals_for'],
                    'standing' => $standing,
                ];
            }
            uasort($teamData, function ($a, $b) {
                if ($a['points'] !== $b['points']) {
                    return $b['points'] - $a['points'];
                }
                if ($a['goal_difference'] !== $b['goal_difference']) {
                    return $b['goal_difference'] - $a['goal_difference'];
                }
                return $b['goals_for'] - $a['goals_for'];
            });
            $scores = [];
            foreach ($teamData as $teamId => $data) {
                $standing = $data['standing'];
                $pointsWeight = ($standing['points'] / max($totalPoints, 1)) * config('league.points_weight');
                $goalsForWeight = ($standing['goals_for'] / max($totalGoalsFor, 1)) * config('league.goals_for_weight');
                $goalsAgainstWeight = (1 - ($standing['goals_against'] / max($totalGoalsAgainst, 1))) * config('league.goals_against_weight');
                $weightedScore = $pointsWeight + $goalsForWeight + $goalsAgainstWeight;
                $scores[$teamId] = $weightedScore;
            }
            $sortedScores = [];
            foreach (array_keys($teamData) as $teamId) {
                $sortedScores[$teamId] = $scores[$teamId];
            }
            $totalScore = array_sum($sortedScores);
            $predictions = [];
            if ($totalScore > 0) {
                $lastValue = null;
                foreach ($sortedScores as $teamId => $score) {
                    $predictions[$teamId] = round(($score / $totalScore) * 100, 2);
                    if ($lastValue !== null && $teamData[$teamId]['points'] < $lastValue['points']) {
                        if ($predictions[$teamId] > $predictions[$lastValue['id']]) {
                            $tmp = $predictions[$teamId];
                            $predictions[$teamId] = $predictions[$lastValue['id']];
                            $predictions[$lastValue['id']] = $tmp;
                        }
                    }
                    $lastValue = ['id' => $teamId, 'points' => $teamData[$teamId]['points']];
                }
            }
            foreach ($standings as $standing) {
                $teamId = $standing['team']->id;
                if (!in_array($teamId, $inRace)) {
                    $predictions[$teamId] = 0.0;
                }
            }
        }

        foreach ($predictions as $teamId => $percentage) {
            Prediction::updateOrCreate(
                [
                    'league_id' => $league->id,
                    'week' => $league->current_week,
                    'team_id' => $teamId,
                ],
                [
                    'percentage' => $percentage,
                ]
            );
        }
    }

    /**
     * Find teams that mathematically cannot be caught
     *
     * @param array $standings Current standings
     * @param array $maxPossiblePoints Maximum possible points for each team
     * @return array Array of team IDs that are definitive winners
     */
    private function findDefinitiveWinners(array $standings, array $maxPossiblePoints): array
    {
        $definitiveWinners = [];

        foreach ($standings as $index => $standing) {
            $teamId = $standing['team']->id;
            $currentPoints = $standing['points'];

            $canBeCaught = false;

            foreach ($standings as $otherIndex => $otherStanding) {
                if ($index === $otherIndex)
                    continue;

                $otherTeamId = $otherStanding['team']->id;
                $otherMaxPoints = $maxPossiblePoints[$otherTeamId];

                // If any team below can reach or exceed this team's current points
                if ($otherMaxPoints >= $currentPoints) {
                    $canBeCaught = true;
                    break;
                }
            }

            if (!$canBeCaught) {
                $definitiveWinners[] = $teamId;
            }
        }

        return $definitiveWinners;
    }

    /**
     * Calculate weighted predictions based on team performance and remaining matches
     *
     * @param League $league The league
     * @param array $standings Current standings
     * @param int $remainingWeeks Number of remaining weeks
     * @return array Array of predictions indexed by team ID
     */
    private function calculateWeightedPredictions(League $league, array $standings, int $remainingWeeks): array
    {
        $predictions = [];
        $totalPoints = 0;
        $totalGoalsFor = 0;
        $totalGoalsAgainst = 0;

        foreach ($standings as $standing) {
            $totalPoints += $standing['points'];
            $totalGoalsFor += $standing['goals_for'];
            $totalGoalsAgainst += $standing['goals_against'];
        }

        foreach ($standings as $standing) {
            $teamId = $standing['team']->id;

            $pointsScore = ($standing['points'] / max($totalPoints, 1)) * config('league.points_weight');
            $goalsForScore = ($standing['goals_for'] / max($totalGoalsFor, 1)) * config('league.goals_for_weight');
            $goalsAgainstScore = (1 - ($standing['goals_against'] / max($totalGoalsAgainst, 1))) * config('league.goals_against_weight');

            $baseScore = $pointsScore + $goalsForScore + $goalsAgainstScore;

            $remainingMatchesFactor = $this->calculateRemainingMatchesFactor($league, $standing['team'], $remainingWeeks);

            $combinedScore = ($baseScore * 0.6) + ($remainingMatchesFactor * 0.4);

            $predictions[$teamId] = $combinedScore;
        }

        $totalScore = array_sum($predictions);
        if ($totalScore > 0) {
            foreach ($predictions as $teamId => $score) {
                $predictions[$teamId] = round(($score / $totalScore) * 100, 2);
            }
        }

        return $predictions;
    }

    /**
     * Calculate factor based on remaining matches and opponents
     *
     * @param League $league The league
     * @param Team $team The team to calculate for
     * @param int $remainingWeeks Number of remaining weeks
     * @return float Factor between 0 and 1
     */
    private function calculateRemainingMatchesFactor(League $league, $team, int $remainingWeeks): float
    {
        if ($remainingWeeks === 0) {
            return 1.0;
        }

        $remainingMatches = $this->getRemainingMatches($league, $team);

        if (empty($remainingMatches)) {
            return 1.0;
        }

        $totalDifficulty = 0;
        $matchCount = 0;

        foreach ($remainingMatches as $match) {
            $opponentId = $match['home_team_id'] == $team->id ? $match['away_team_id'] : $match['home_team_id'];
            $difficulty = $this->calculateMatchDifficulty($league, $team->id, $opponentId);
            $totalDifficulty += $difficulty;
            $matchCount++;
        }

        $averageDifficulty = $matchCount > 0 ? $totalDifficulty / $matchCount : 1.0;

        return max(0, 1 - $averageDifficulty);
    }

    /**
     * Get remaining matches for a team
     *
     * @param League $league The league
     * @param Team $team The team
     * @return array Array of remaining matches
     */
    private function getRemainingMatches(League $league, $team): array
    {
        return $league->matches()
            ->where('week', '>', $league->current_week)
            ->where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Calculate difficulty of a match based on opponent strength
     *
     * @param League $league The league
     * @param int $teamId The team ID
     * @param int $opponentId The opponent ID
     * @return float Difficulty factor between 0 and 1
     */
    private function calculateMatchDifficulty(League $league, int $teamId, int $opponentId): float
    {
        $standings = $this->standingService->calculateStandings($league);

        $teamPosition = null;
        $opponentPosition = null;

        foreach ($standings as $index => $standing) {
            if ($standing['team']->id === $teamId) {
                $teamPosition = $index + 1;
            }
            if ($standing['team']->id === $opponentId) {
                $opponentPosition = $index + 1;
            }
        }

        if ($teamPosition === null || $opponentPosition === null) {
            return 0.5;
        }

        $totalTeams = count($standings);

        $teamStrength = 1 - (($teamPosition - 1) / ($totalTeams - 1));
        $opponentStrength = 1 - (($opponentPosition - 1) / ($totalTeams - 1));

        $strengthDifference = $opponentStrength - $teamStrength;

        return max(0, min(1, 0.5 + $strengthDifference));
    }
}
