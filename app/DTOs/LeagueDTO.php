<?php

namespace App\DTOs;

use App\Http\Requests\StoreLeagueRequest;

/**
 * Data Transfer Object for League data
 */
class LeagueDTO
{
    /**
     * Create a new LeagueDTO instance
     *
     * @param string $name The name of the league
     * @param array $teamIds Array of team IDs to be included in the league
     */
    public function __construct(
        public readonly string $name,
        public readonly array $teamIds,
    ) {
    }

    /**
     * Create a LeagueDTO from a StoreLeagueRequest
     *
     * @param StoreLeagueRequest $request The request containing league data
     * @return self
     */
    public static function fromRequest(StoreLeagueRequest $request): self
    {
        return new self($request->name, $request->team_ids);
    }

    /**
     * Convert the DTO to an array representation
     *
     * @return array Array representation of the league data
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'teamIds' => $this->teamIds,
        ];
    }
}