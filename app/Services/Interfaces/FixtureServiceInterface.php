<?php

namespace App\Services\Interfaces;

use App\Models\League;
use App\Services\DTOs\MatchDTO;

interface FixtureServiceInterface
{
    /**
     * Generate fixtures for a league
     */
    public function generateFixtures(League $league, array $teamIds): void;

    /**
     * Generate all possible matches between teams
     */
    public function generateAllPossibleMatches(array $teamIds): array;

    /**
     * Create a match DTO
     */
    public function createMatch(int $homeTeamId, int $awayTeamId, int $week): MatchDTO;
}
