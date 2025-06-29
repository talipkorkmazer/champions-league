<?php

namespace App\DTOs;

/**
 * Data Transfer Object for Match data
 */
class MatchDTO
{
    /**
     * Create a new MatchDTO instance
     *
     * @param int $homeTeamId The ID of the home team
     * @param int $awayTeamId The ID of the away team
     * @param int $week The week number of the match
     * @param bool $isPlayed Whether the match has been played
     * @param int|null $homeScore The home team's score
     * @param int|null $awayScore The away team's score
     */
    public function __construct(
        public int $homeTeamId,
        public int $awayTeamId,
        public int $week,
        public bool $isPlayed = false,
        public ?int $homeScore = null,
        public ?int $awayScore = null
    ) {
    }

    /**
     * Check if the given team is the home team in this match
     *
     * @param int $teamId The team ID to check
     * @return bool True if the team is the home team
     */
    public function isHomeMatch(int $teamId): bool
    {
        return $this->homeTeamId === $teamId;
    }

    /**
     * Check if the given team is the away team in this match
     *
     * @param int $teamId The team ID to check
     * @return bool True if the team is the away team
     */
    public function isAwayMatch(int $teamId): bool
    {
        return $this->awayTeamId === $teamId;
    }

    /**
     * Get the score for a specific team in this match
     *
     * @param int $teamId The team ID to get the score for
     * @return int|null The team's score or null if team is not in this match
     */
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

    /**
     * Get the opponent's score for a specific team in this match
     *
     * @param int $teamId The team ID to get the opponent's score for
     * @return int|null The opponent's score or null if team is not in this match
     */
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

    /**
     * Convert the DTO to an array representation
     *
     * @return array Array representation of the match data
     */
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
