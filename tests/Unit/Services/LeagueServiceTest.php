<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LeagueService;
use App\Services\FixtureService;
use App\Services\StandingService;
use App\Services\PredictionService;
use App\Services\LeagueUtility;
use App\DTOs\LeagueDTO;
use App\Models\League;
use App\Models\Team;
use App\Models\LeagueMatch;
use App\Models\Prediction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class LeagueServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeagueService $leagueService;
    private $fixtureService;
    private $standingService;
    private $predictionService;
    private $leagueUtility;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureService = Mockery::mock(FixtureService::class);
        $this->standingService = Mockery::mock(StandingService::class);
        $this->predictionService = Mockery::mock(PredictionService::class);
        $this->leagueUtility = Mockery::mock(LeagueUtility::class);

        $this->leagueService = new LeagueService(
            $this->fixtureService,
            $this->standingService,
            $this->predictionService,
            $this->leagueUtility
        );
    }

    public function test_create_league_successfully()
    {
        $teams = Team::factory(4)->create();
        $teamIds = $teams->pluck('id')->toArray();
        $leagueDTO = new LeagueDTO('Test League', $teamIds);

        $expectedMatches = [
            [
                'league_id' => 1,
                'week' => 1,
                'home_team_id' => $teamIds[0],
                'away_team_id' => $teamIds[1],
                'is_played' => false
            ]
        ];

        $this->fixtureService->shouldReceive('generateFixtures')
            ->once()
            ->with(Mockery::type(League::class), $teamIds)
            ->andReturn($expectedMatches);

        $league = $this->leagueService->createLeague($leagueDTO);

        $this->assertInstanceOf(League::class, $league);
        $this->assertEquals('Test League', $league->name);
        $this->assertEquals(0, $league->current_week);
        $this->assertCount(4, $league->teams);
        $this->assertDatabaseHas('leagues', ['name' => 'Test League']);
    }

    public function test_reset_league_successfully()
    {
        $league = League::factory()->create(['current_week' => 3]);
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'home_score' => 2,
            'away_score' => 1,
            'is_played' => true
        ]);

        $this->leagueService->resetLeague($league);

        $this->assertEquals(0, $league->fresh()->current_week);
        $this->assertDatabaseHas('league_matches', [
            'id' => $match->id,
            'is_played' => false,
            'home_score' => null,
            'away_score' => null
        ]);
    }

    public function test_get_league_utility_data()
    {
        $league = League::factory()->create(['current_week' => 2]);

        $this->leagueUtility->shouldReceive('getLeagueStatus')
            ->once()
            ->with(2)
            ->andReturn(\App\Enums\LeagueStatus::IN_PROGRESS);

        $this->leagueUtility->shouldReceive('getProgressPercentage')
            ->once()
            ->with(2)
            ->andReturn(50.0);

        $this->leagueUtility->shouldReceive('getStatusLabel')
            ->once()
            ->with(2)
            ->andReturn('In Progress');

        $this->leagueUtility->shouldReceive('canSimulate')
            ->once()
            ->with(2)
            ->andReturn(true);

        $this->leagueUtility->shouldReceive('getRemainingWeeks')
            ->once()
            ->with(2)
            ->andReturn(4);

        $this->leagueUtility->shouldReceive('getTotalWeeks')
            ->once()
            ->andReturn(6);

        $utilityData = $this->leagueService->getLeagueUtilityData($league);

        $this->assertEquals(\App\Enums\LeagueStatus::IN_PROGRESS->value, $utilityData->status);
        $this->assertEquals(50.0, $utilityData->progressPercentage);
        $this->assertEquals('In Progress', $utilityData->statusLabel);
        $this->assertTrue($utilityData->canSimulate);
        $this->assertEquals(4, $utilityData->remainingWeeks);
        $this->assertEquals(6, $utilityData->totalWeeks);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
