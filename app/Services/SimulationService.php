<?php

namespace App\Services;

use App\Models\League;
use App\Models\Prediction;
use App\Models\LeagueMatch;
use App\Services\Interfaces\SimulationServiceInterface;

/**
 * Service class for simulating league matches and calculating predictions
 */
class SimulationService implements SimulationServiceInterface
{
    /**
     * Simulate all matches for the next week in a league
     *
     * @param League $league The league to simulate matches for
     * @return void
     */
    public function simulateWeek(League $league): void
    {
        $currentWeek = $league->current_week + 1;
        
        if ($currentWeek > config('league.total_weeks')) {
            return;
        }

        $matches = $league->matches()
            ->where('week', $currentWeek)
            ->where('is_played', false)
            ->with(['homeTeam', 'awayTeam'])
            ->get();

        foreach ($matches as $match) {
            $this->simulateMatch($match);
        }

        $league->update(['current_week' => $currentWeek]);

        $this->calculatePredictions($league);
    }

    /**
     * Simulate all remaining matches in a league
     *
     * @param League $league The league to simulate all matches for
     * @return void
     */
    public function simulateAll(League $league): void
    {
        for ($week = $league->current_week + 1; $week <= config('league.total_weeks'); $week++) {
            $matches = $league->matches()
                ->where('week', $week)
                ->where('is_played', false)
                ->with(['homeTeam', 'awayTeam'])
                ->get();

            foreach ($matches as $match) {
                $this->simulateMatch($match);
            }

            $league->update(['current_week' => $week]);

            $this->calculatePredictions($league);
        }
    }

    /**
     * Simulate a single match between two teams
     *
     * @param LeagueMatch $match The match to simulate
     * @return void
     */
    public function simulateMatch(LeagueMatch $match): void
    {
        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;

        $homeStrength = $homeTeam->strength;
        $awayStrength = $awayTeam->strength;

        $homeStrength *= config('league.home_advantage_multiplier');

        $homeRandomFactor = rand(config('league.random_factor_min') * 100, config('league.random_factor_max') * 100) / 100;
        $awayRandomFactor = rand(config('league.random_factor_min') * 100, config('league.random_factor_max') * 100) / 100;

        $homeFinalStrength = $homeStrength * $homeRandomFactor;
        $awayFinalStrength = $awayStrength * $awayRandomFactor;

        $strengthDiff = $homeFinalStrength - $awayFinalStrength;
        
        $homeGoals = max(0, min(config('league.base_goals_max'), round(($homeFinalStrength / 100) * config('league.base_goals_max'))));
        $awayGoals = max(0, min(config('league.base_goals_max'), round(($awayFinalStrength / 100) * config('league.base_goals_max'))));

        if ($strengthDiff > config('league.strength_difference_threshold')) {
            $homeGoals = min(config('league.base_goals_max') + 1, $homeGoals + 1);
        } elseif ($strengthDiff < -config('league.strength_difference_threshold')) {
            $awayGoals = min(config('league.base_goals_max') + 1, $awayGoals + 1);
        }

        $homeGoals += rand(-config('league.goal_randomness_range'), config('league.goal_randomness_range'));
        $awayGoals += rand(-config('league.goal_randomness_range'), config('league.goal_randomness_range'));

        $homeGoals = max(0, $homeGoals);
        $awayGoals = max(0, $awayGoals);

        $match->update([
            'home_score' => $homeGoals,
            'away_score' => $awayGoals,
            'is_played' => true
        ]);
    }

    /**
     * Calculate predictions for the league based on current standings
     *
     * @param League $league The league to calculate predictions for
     * @return void
     */
    public function calculatePredictions(League $league): void
    {
        // TODO: Implement calculatePredictions() method.
    }
} 