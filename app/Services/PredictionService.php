<?php

namespace App\Services;

use App\Models\Prediction;
use App\Services\Interfaces\PredictionServiceInterface;

class PredictionService implements PredictionServiceInterface
{
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

    public function getPredictions(int $leagueId, int $currentWeek): array
    {
        return Prediction::where('league_id', $leagueId)
            ->where('week', $currentWeek)
            ->get()
            ->keyBy('team_id')
            ->map(fn($prediction) => $prediction->percentage)
            ->toArray();
    }

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
