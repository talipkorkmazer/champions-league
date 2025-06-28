<?php

namespace App\Services\Interfaces;

interface PredictionServiceInterface
{
    /**
     * Add predictions to standings
     */
    public function addPredictionsToStandings(array $standings, int $leagueId, int $currentWeek): array;

    /**
     * Get predictions for a league and week
     */
    public function getPredictions(int $leagueId, int $currentWeek): array;

    /**
     * Calculate championship percentages for teams
     */
    public function calculateChampionshipPercentages(array $standings, int $leagueId, int $currentWeek): array;
}
