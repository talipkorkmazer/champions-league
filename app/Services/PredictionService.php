<?php

namespace App\Services;

use App\Models\Prediction;
use App\Services\Interfaces\PredictionServiceInterface;

/**
 * Service class for calculating and managing championship predictions
 */
class PredictionService implements PredictionServiceInterface
{
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
}
