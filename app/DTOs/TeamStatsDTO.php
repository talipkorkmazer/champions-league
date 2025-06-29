<?php

namespace App\DTOs;

use App\Models\Team;

/**
 * Data Transfer Object for Team Statistics
 */
class TeamStatsDTO
{
    /**
     * Create a new TeamStatsDTO instance
     *
     * @param Team $team The team this stats belong to
     * @param int $played Number of matches played
     * @param int $won Number of matches won
     * @param int $drawn Number of matches drawn
     * @param int $lost Number of matches lost
     * @param int $goalsFor Number of goals scored
     * @param int $goalsAgainst Number of goals conceded
     * @param int $points Total points earned
     * @param int $goalDifference Goal difference (goals for - goals against)
     * @param float $championshipPercentage Percentage chance of winning championship
     */
    public function __construct(
        public Team $team,
        public int $played = 0,
        public int $won = 0,
        public int $drawn = 0,
        public int $lost = 0,
        public int $goalsFor = 0,
        public int $goalsAgainst = 0,
        public int $points = 0,
        public int $goalDifference = 0,
        public float $championshipPercentage = 0.0
    ) {
    }

    /**
     * Calculate total points based on wins and draws
     *
     * @param int $pointsForWin Points awarded for a win
     * @param int $pointsForDraw Points awarded for a draw
     * @return void
     */
    public function calculatePoints(int $pointsForWin = 3, int $pointsForDraw = 1): void
    {
        $this->points = ($this->won * $pointsForWin) + ($this->drawn * $pointsForDraw);
    }

    /**
     * Calculate goal difference (goals for - goals against)
     *
     * @return void
     */
    public function calculateGoalDifference(): void
    {
        $this->goalDifference = $this->goalsFor - $this->goalsAgainst;
    }

    /**
     * Add match result to team statistics
     *
     * @param int $goalsFor Goals scored by the team
     * @param int $goalsAgainst Goals conceded by the team
     * @param bool $isHomeMatch Whether this was a home match
     * @return void
     */
    public function addMatchResult(int $goalsFor, int $goalsAgainst, bool $isHomeMatch): void
    {
        $this->played++;

        if ($isHomeMatch) {
            $this->goalsFor += $goalsFor;
            $this->goalsAgainst += $goalsAgainst;
        } else {
            $this->goalsFor += $goalsAgainst;
            $this->goalsAgainst += $goalsFor;
        }

        $this->updateMatchStats($goalsFor, $goalsAgainst, $isHomeMatch);
    }

    /**
     * Update match statistics based on result
     *
     * @param int $teamGoals Goals scored by the team
     * @param int $opponentGoals Goals scored by the opponent
     * @param bool $isHomeMatch Whether this was a home match
     * @return void
     */
    private function updateMatchStats(int $teamGoals, int $opponentGoals, bool $isHomeMatch): void
    {
        $actualTeamGoals = $isHomeMatch ? $teamGoals : $opponentGoals;
        $actualOpponentGoals = $isHomeMatch ? $opponentGoals : $teamGoals;

        if ($actualTeamGoals > $actualOpponentGoals) {
            $this->won++;
        } elseif ($actualTeamGoals === $actualOpponentGoals) {
            $this->drawn++;
        } else {
            $this->lost++;
        }
    }

    /**
     * Convert the DTO to an array representation
     *
     * @return array Array representation of the team stats
     */
    public function toArray(): array
    {
        return [
            'team' => $this->team,
            'played' => $this->played,
            'won' => $this->won,
            'drawn' => $this->drawn,
            'lost' => $this->lost,
            'goals_for' => $this->goalsFor,
            'goals_against' => $this->goalsAgainst,
            'goal_difference' => $this->goalDifference,
            'points' => $this->points,
            'championship_percentage' => $this->championshipPercentage,
        ];
    }
}
