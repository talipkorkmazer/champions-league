<?php

namespace App\Services;

use App\Models\League;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
use App\Services\Interfaces\FixtureServiceInterface;
use App\Services\Interfaces\StandingServiceInterface;
use App\Services\Interfaces\PredictionServiceInterface;

class LeagueService
{
    public function __construct(
        private FixtureServiceInterface $fixtureService,
        private StandingServiceInterface $standingService,
        private PredictionServiceInterface $predictionService
    ) {
    }

    public function createLeague(string $name, array $teamIds): League
    {
        return DB::transaction(function () use ($name, $teamIds) {
            $league = League::create([
                'name' => $name,
                'current_week' => 0
            ]);

            $league->teams()->attach($teamIds);

            $this->fixtureService->generateFixtures($league, $teamIds);

            return $league;
        });
    }

    public function resetLeague(League $league): void
    {
        DB::transaction(function () use ($league) {
            $league->update(['current_week' => 0]);
            
            $league->matches()->update([
                'is_played' => false,
                'home_score' => null,
                'away_score' => null,
            ]);
            
            $this->deleteLeaguePredictions($league);
        });
    }

    public function getLeagueWithStandings(League $league): array
    {
        $league->load(['teams', 'matches.homeTeam', 'matches.awayTeam']);
        
        $standings = $this->standingService->calculateStandings($league);
        $standingsWithPredictions = $this->predictionService->addPredictionsToStandings(
            $standings, 
            $league->id, 
            $league->current_week
        );
        
        return [
            'league' => $league,
            'standings' => $standingsWithPredictions
        ];
    }

    private function deleteLeaguePredictions(League $league): void
    {
        if (method_exists($league, 'predictions')) {
            $league->predictions()->delete();
        } else {
            Prediction::where('league_id', $league->id)->delete();
        }
    }
}
