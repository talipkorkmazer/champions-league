<?php

namespace App\DTOs;

/**
 * Data Transfer Object for league utility data
 */
class LeagueUtilityDTO
{
    public function __construct(
        public readonly string $status,
        public readonly int $progressPercentage,
        public readonly string $statusLabel,
        public readonly bool $canSimulate,
        public readonly int $remainingWeeks,
        public readonly int $totalWeeks,
    ) {
    }

    /**
     * Convert the DTO to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'progressPercentage' => $this->progressPercentage,
            'statusLabel' => $this->statusLabel,
            'canSimulate' => $this->canSimulate,
            'remainingWeeks' => $this->remainingWeeks,
            'totalWeeks' => $this->totalWeeks,
        ];
    }
} 