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
    'max_goal_probability' => env('LEAGUE_MAX_GOAL_PROBABILITY', 0.8),
    'max_goals_bonus' => env('LEAGUE_MAX_GOALS_BONUS', 4),
    'strength_to_goal_multiplier' => env('LEAGUE_STRENGTH_TO_GOAL_MULTIPLIER', 0.6),
    'strength_bonus_range' => env('LEAGUE_STRENGTH_BONUS_RANGE', 1),
    'goal_distribution' => [
        'zero_goals_threshold' => env('LEAGUE_ZERO_GOALS_THRESHOLD', 30),
        'one_goal_threshold' => env('LEAGUE_ONE_GOAL_THRESHOLD', 60),
        'two_goals_threshold' => env('LEAGUE_TWO_GOALS_THRESHOLD', 80),
        'three_goals_threshold' => env('LEAGUE_THREE_GOALS_THRESHOLD', 90),
        'four_goals_threshold' => env('LEAGUE_FOUR_GOALS_THRESHOLD', 95),
        'five_goals_threshold' => env('LEAGUE_FIVE_GOALS_THRESHOLD', 98),
        'six_goals_threshold' => env('LEAGUE_SIX_GOALS_THRESHOLD', 99),
    ],
    'max_championship_probability' => env('LEAGUE_MAX_CHAMPIONSHIP_PROBABILITY', 95),
    'min_championship_probability' => env('LEAGUE_MIN_CHAMPIONSHIP_PROBABILITY', 5),
    'position_factor_decrease' => env('LEAGUE_POSITION_FACTOR_DECREASE', 0.1),
    'strength_weight' => env('LEAGUE_STRENGTH_WEIGHT', 0.6),
    'position_weight' => env('LEAGUE_POSITION_WEIGHT', 0.4),
    'monte_carlo_simulations' => env('LEAGUE_MONTE_CARLO_SIMULATIONS', 2000),
]; 