<?php

namespace App\Services\Interfaces;

use App\Models\League;
use App\Models\LeagueMatch;

/**
 * Interface for simulation service operations
 */
interface SimulationServiceInterface
{
    /**
     * Simulate all matches for the next week in a league
     *
     * @param League $league The league to simulate matches for
     * @return void
     */
    public function simulateWeek(League $league): void;
    
    /**
     * Simulate all remaining matches in a league
     *
     * @param League $league The league to simulate all matches for
     * @return void
     */
    public function simulateAll(League $league): void;
    
    /**
     * Simulate a single match between two teams
     *
     * @param LeagueMatch $match The match to simulate
     * @return void
     */
    public function simulateMatch(LeagueMatch $match): void;
    
    /**
     * Calculate predictions for the league based on current standings
     *
     * @param League $league The league to calculate predictions for
     * @return void
     */
    public function calculatePredictions(League $league): void;
} 