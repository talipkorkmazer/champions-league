<?php

namespace App\Services\DTOs;

class MatchDTO
{
    public function __construct(
        public int $homeTeamId,
        public int $awayTeamId,
        public int $week,
        public bool $isPlayed = false,
        public ?int $homeScore = null,
        public ?int $awayScore = null
    ) {
    }

    public function isHomeMatch(int $teamId): bool
    {
        return $this->homeTeamId === $teamId;
    }

    public function isAwayMatch(int $teamId): bool
    {
        return $this->awayTeamId === $teamId;
    }

    public function getTeamScore(int $teamId): ?int
    {
        if ($this->isHomeMatch($teamId)) {
            return $this->homeScore;
        }

        if ($this->isAwayMatch($teamId)) {
            return $this->awayScore;
        }

        return null;
    }

    public function getOpponentScore(int $teamId): ?int
    {
        if ($this->isHomeMatch($teamId)) {
            return $this->awayScore;
        }

        if ($this->isAwayMatch($teamId)) {
            return $this->homeScore;
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'home_team_id' => $this->homeTeamId,
            'away_team_id' => $this->awayTeamId,
            'week' => $this->week,
            'is_played' => $this->isPlayed,
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
        ];
    }
}
