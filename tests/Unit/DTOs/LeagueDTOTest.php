<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\LeagueDTO;
use App\Http\Requests\StoreLeagueRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeagueDTOTest extends TestCase
{
    use RefreshDatabase;

    public function test_league_dto_creation()
    {
        $name = 'Test League';
        $teamIds = [1, 2, 3, 4];

        $dto = new LeagueDTO($name, $teamIds);

        $this->assertEquals($name, $dto->name);
        $this->assertEquals($teamIds, $dto->teamIds);
    }

    public function test_league_dto_from_request()
    {
        $requestData = [
            'name' => 'Test League',
            'team_ids' => [1, 2, 3, 4]
        ];

        $request = new StoreLeagueRequest();
        $request->merge($requestData);

        $dto = LeagueDTO::fromRequest($request);

        $this->assertEquals('Test League', $dto->name);
        $this->assertEquals([1, 2, 3, 4], $dto->teamIds);
    }

    public function test_league_dto_to_array()
    {
        $dto = new LeagueDTO('Test League', [1, 2, 3, 4]);

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test League', $array['name']);
        $this->assertEquals([1, 2, 3, 4], $array['teamIds']);
    }
}
