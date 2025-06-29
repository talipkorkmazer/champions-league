<?php

namespace App\Enums;

enum LeagueStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    /**
     * Get the display label for the status
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'Not Started',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }
}
