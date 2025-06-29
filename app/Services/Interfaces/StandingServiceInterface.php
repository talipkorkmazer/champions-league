<?php

namespace App\Services\Interfaces;

use App\Models\League;
use App\Models\Team;
use App\DTOs\TeamStatsDTO;

/**
 * Interface for standing service operations
 */
interface StandingServiceInterface
{
    /**
     * Calculate standings for a league
     *
     * @param League $league The league to calculate standings for
     * @return array Array of team standings sorted by position
     */
    public function calculateStandings(League $league): array;

    /**
     * Calculate team statistics for a specific team in a league
     *
     * @param Team $team The team to calculate statistics for
     * @param League $league The league to calculate statistics for
     * @return TeamStatsDTO The calculated team statistics
     */
    public function calculateTeamStats(Team $team, League $league): TeamStatsDTO;

    /**
     * Sort standings by points, goal difference, and goals scored
     *
     * @param array $standings Array of team standings to sort
     * @return array Sorted array of team standings
     */
    public function sortStandings(array $standings): array;
}
