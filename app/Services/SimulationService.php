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
    public function __construct(
        private readonly PredictionService $predictionService
    ) {}

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

        if ($currentWeek >= config('league.min_week_for_predictions')) {
            $this->predictionService->calculatePredictions($league);
        }
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
        }
        $this->predictionService->calculatePredictions($league);
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

        $homeGoalProbability = min(config('league.max_goal_probability'), ($homeFinalStrength / 100) * config('league.strength_to_goal_multiplier'));
        $awayGoalProbability = min(config('league.max_goal_probability'), ($awayFinalStrength / 100) * config('league.strength_to_goal_multiplier'));

        $homeGoals = $this->generateRealisticGoals($homeGoalProbability);
        $awayGoals = $this->generateRealisticGoals($awayGoalProbability);

        $strengthDiff = $homeFinalStrength - $awayFinalStrength;
        if ($strengthDiff > config('league.strength_difference_threshold')) {
            $homeGoals = min(config('league.max_goals_bonus'), $homeGoals + (rand(0, config('league.strength_bonus_range'))));
        } elseif ($strengthDiff < -config('league.strength_difference_threshold')) {
            $awayGoals = min(config('league.max_goals_bonus'), $awayGoals + (rand(0, config('league.strength_bonus_range'))));
        }

        $homeGoals += rand(-config('league.goal_randomness_range'), config('league.goal_randomness_range'));
        $awayGoals += rand(-config('league.goal_randomness_range'), config('league.goal_randomness_range'));

        $homeGoals = max(0, min(config('league.base_goals_max'), $homeGoals));
        $awayGoals = max(0, min(config('league.base_goals_max'), $awayGoals));

        $match->update([
            'home_score' => $homeGoals,
            'away_score' => $awayGoals,
            'is_played' => true
        ]);
    }

    /**
     * Generate realistic number of goals based on probability
     *
     * @param float $probability Goal probability (0-1)
     * @return int Number of goals
     */
    private function generateRealisticGoals(float $probability): int
    {
        $value = rand(1, 100) * $probability;
        $distribution = config('league.goal_distribution');

        if ($value <= $distribution['zero_goals_threshold'])
            return 0;
        if ($value <= $distribution['one_goal_threshold'])
            return 1;
        if ($value <= $distribution['two_goals_threshold'])
            return 2;
        if ($value <= $distribution['three_goals_threshold'])
            return 3;
        if ($value <= $distribution['four_goals_threshold'])
            return 4;
        if ($value <= $distribution['five_goals_threshold'])
            return 5;
        if ($value <= $distribution['six_goals_threshold'])
            return 6;

        return 7;
    }
}
