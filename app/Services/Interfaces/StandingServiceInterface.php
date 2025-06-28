<?php

namespace App\Services\Interfaces;

use App\Models\League;
use App\Services\DTOs\TeamStatsDTO;

interface StandingServiceInterface
{
    /**
     * Calculate standings for a league
     */
    public function calculateStandings(League $league): array;

    /**
     * Calculate team statistics
     */
    public function calculateTeamStats(int $teamId, int $leagueId): TeamStatsDTO;

    /**
     * Sort standings by points, goal difference, and goals scored
     */
    public function sortStandings(array $standings): array;
}
