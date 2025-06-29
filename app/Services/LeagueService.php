<?php

namespace App\Services;

use App\Models\League;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
use App\Services\Interfaces\FixtureServiceInterface;
use App\Services\Interfaces\StandingServiceInterface;
use App\Services\Interfaces\PredictionServiceInterface;
use App\DTOs\LeagueDTO;
use App\DTOs\LeagueUtilityDTO;
use App\Models\LeagueMatch;
use App\Services\LeagueUtility;

/**
 * Service class for managing league operations
 */
class LeagueService
{
    /**
     * Create a new LeagueService instance
     *
     * @param FixtureServiceInterface $fixtureService Service for generating fixtures
     * @param StandingServiceInterface $standingService Service for calculating standings
     * @param PredictionServiceInterface $predictionService Service for calculating predictions
     * @param LeagueUtility $leagueUtility Service for league utility calculations
     */
    public function __construct(
        private FixtureServiceInterface $fixtureService,
        private StandingServiceInterface $standingService,
        private PredictionServiceInterface $predictionService,
        private LeagueUtility $leagueUtility
    ) {
    }

    /**
     * Create a new league with teams and fixtures
     *
     * @param LeagueDTO $leagueDTO The league data transfer object
     * @return League The created league
     */
    public function createLeague(LeagueDTO $leagueDTO): League
    {
        return DB::transaction(callback: function () use ($leagueDTO): League {
            $league = League::create(attributes: [
                'name' => $leagueDTO->name,
                'current_week' => 0
            ]);

            $league->teams()->attach($leagueDTO->teamIds);

            $matches = $this->fixtureService->generateFixtures(league: $league, teamIds: $leagueDTO->teamIds);
            LeagueMatch::upsert(values: $matches, uniqueBy: ['league_id', 'week', 'home_team_id', 'away_team_id']);

            return $league;
        });
    }

    /**
     * Reset a league to its initial state
     *
     * @param League $league The league to reset
     * @return void
     */
    public function resetLeague(League $league): void
    {
        DB::transaction(callback: function () use ($league): void {
            $league->update(['current_week' => 0]);

            $league->matches()->update([
                'is_played' => false,
                'home_score' => null,
                'away_score' => null,
            ]);

            $this->deleteLeaguePredictions($league);
        });
    }

    /**
     * Delete all predictions for a league
     *
     * @param League $league The league to delete predictions for
     * @return void
     */
    private function deleteLeaguePredictions(League $league): void
    {
        if (method_exists($league, 'predictions')) {
            $league->predictions()->delete();
        } else {
            Prediction::where('league_id', $league->id)->delete();
        }
    }

    /**
     * Get league data with calculated standings
     *
     * @param League $league The league to get data for
     * @return array Array containing league and standings data
     */
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

    /**
     * Get matches for a specific week
     *
     * @param League $league The league
     * @param int $week The week number
     * @return \Illuminate\Database\Eloquent\Collection The matches for the week
     */
    public function getMatchesByWeek(League $league, int $week)
    {
        return $league->matches()->where('week', $week)->with(['homeTeam', 'awayTeam'])->get();
    }

    /**
     * Get league utility data for frontend
     *
     * @param League $league The league
     * @return LeagueUtilityDTO The utility data
     */
    public function getLeagueUtilityData(League $league): LeagueUtilityDTO
    {
        $currentWeek = $league->current_week;

        return new LeagueUtilityDTO(
            status: $this->leagueUtility->getLeagueStatus($currentWeek)->value,
            progressPercentage: $this->leagueUtility->getProgressPercentage($currentWeek),
            statusLabel: $this->leagueUtility->getStatusLabel($currentWeek),
            canSimulate: $this->leagueUtility->canSimulate($currentWeek),
            remainingWeeks: $this->leagueUtility->getRemainingWeeks($currentWeek),
            totalWeeks: $this->leagueUtility->getTotalWeeks(),
        );
    }
}
