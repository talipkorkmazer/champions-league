<?php

namespace App\Services;

use App\Enums\LeagueStatus;

/**
 * Utility class for league calculations and status management
 */
class LeagueUtility
{
    /**
     * Get league status based on current week
     *
     * @param int $currentWeek The current week number
     * @return LeagueStatus The league status
     */
    public function getLeagueStatus(int $currentWeek): LeagueStatus
    {
        $totalWeeks = config('league.total_weeks');
        
        if ($currentWeek === 0) return LeagueStatus::NOT_STARTED;
        if ($currentWeek >= $totalWeeks) return LeagueStatus::COMPLETED;
        return LeagueStatus::IN_PROGRESS;
    }

    /**
     * Get progress percentage based on current week
     *
     * @param int $currentWeek The current week number
     * @return int The progress percentage
     */
    public function getProgressPercentage(int $currentWeek): int
    {
        $totalWeeks = config('league.total_weeks');
        return (int) round(($currentWeek / $totalWeeks) * 100);
    }

    /**
     * Get status label for display
     *
     * @param int $currentWeek The current week number
     * @return string The status label
     */
    public function getStatusLabel(int $currentWeek): string
    {
        return $this->getLeagueStatus($currentWeek)->label();
    }

    /**
     * Check if league can be simulated
     *
     * @param int $currentWeek The current week number
     * @return bool Whether simulation is possible
     */
    public function canSimulate(int $currentWeek): bool
    {
        return $currentWeek < config('league.total_weeks');
    }

    /**
     * Get remaining weeks in the league
     *
     * @param int $currentWeek The current week number
     * @return int The number of remaining weeks
     */
    public function getRemainingWeeks(int $currentWeek): int
    {
        return max(0, config('league.total_weeks') - $currentWeek);
    }

    /**
     * Get total weeks from config
     *
     * @return int The total number of weeks
     */
    public function getTotalWeeks(): int
    {
        return config('league.total_weeks');
    }
} 