<?php

namespace App\Services\Interfaces;

use App\Models\League;

/**
 * Interface for prediction service operations
 */
interface PredictionServiceInterface
{
    /**
     * Add predictions to standings
     *
     * @param array $standings Array of team standings
     * @param int $leagueId The ID of the league
     * @param int $currentWeek The current week number
     * @return array Array of standings with predictions added
     */
    public function addPredictionsToStandings(array $standings, int $leagueId, int $currentWeek): array;

    /**
     * Get predictions for a league and week
     *
     * @param int $leagueId The ID of the league
     * @param int $currentWeek The current week number
     * @return array Array of predictions
     */
    public function getPredictions(int $leagueId, int $currentWeek): array;

    /**
     * Calculate championship percentages for teams
     *
     * @param array $standings Array of team standings
     * @param int $leagueId The ID of the league
     * @param int $currentWeek The current week number
     * @return array Array of standings with championship percentages
     */
    public function calculateChampionshipPercentages(array $standings, int $leagueId, int $currentWeek): array;

    /**
     * Calculate predictions for a league
     *
     * @param League $league The league to calculate predictions for
     * @return void
     */
    public function calculatePredictions(League $league): void;
}
