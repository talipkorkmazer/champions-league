<?php

return [
    'total_weeks' => env('LEAGUE_TOTAL_WEEKS', 6),
    'teams_per_league' => env('LEAGUE_TEAMS_PER_LEAGUE', 4),
    'prediction_weeks_threshold' => env('LEAGUE_PREDICTION_WEEKS_THRESHOLD', 3),
    'matches_per_week' => env('LEAGUE_MATCHES_PER_WEEK', 2),
    
    // Simulation constants
    'points_for_win' => env('LEAGUE_POINTS_FOR_WIN', 3),
    'points_for_draw' => env('LEAGUE_POINTS_FOR_DRAW', 1),
    'home_advantage_multiplier' => env('LEAGUE_HOME_ADVANTAGE_MULTIPLIER', 1.1),
    'random_factor_min' => env('LEAGUE_RANDOM_FACTOR_MIN', 0.8),
    'random_factor_max' => env('LEAGUE_RANDOM_FACTOR_MAX', 1.2),
    'strength_difference_threshold' => env('LEAGUE_STRENGTH_DIFFERENCE_THRESHOLD', 20),
    'base_goals_max' => env('LEAGUE_BASE_GOALS_MAX', 7),
    'goal_randomness_range' => env('LEAGUE_GOAL_RANDOMNESS_RANGE', 2),
    'max_championship_probability' => env('LEAGUE_MAX_CHAMPIONSHIP_PROBABILITY', 95),
    'min_championship_probability' => env('LEAGUE_MIN_CHAMPIONSHIP_PROBABILITY', 5),
    'position_factor_decrease' => env('LEAGUE_POSITION_FACTOR_DECREASE', 0.1),
    'strength_weight' => env('LEAGUE_STRENGTH_WEIGHT', 0.6),
    'position_weight' => env('LEAGUE_POSITION_WEIGHT', 0.4),
    'monte_carlo_simulations' => env('LEAGUE_MONTE_CARLO_SIMULATIONS', 2000),
]; 