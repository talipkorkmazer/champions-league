<?php

namespace App\Services\DTOs;

use App\Models\Team;

class TeamStatsDTO
{
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

    public function calculatePoints(int $pointsForWin = 3, int $pointsForDraw = 1): void
    {
        $this->points = ($this->won * $pointsForWin) + ($this->drawn * $pointsForDraw);
    }

    public function calculateGoalDifference(): void
    {
        $this->goalDifference = $this->goalsFor - $this->goalsAgainst;
    }

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
