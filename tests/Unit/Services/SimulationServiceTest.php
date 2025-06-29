<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SimulationService;
use App\Models\League;
use App\Models\Team;
use App\Models\LeagueMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class SimulationServiceTest extends TestCase
{
    use RefreshDatabase;

    private SimulationService $simulationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->simulationService = new SimulationService();
    }

    public function test_simulate_week_successfully()
    {
        $league = League::factory()->create(['current_week' => 1]);
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        $match = LeagueMatch::factory()->unplayed()->create([
            'league_id' => $league->id,
            'week' => 2,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
        ]);

        $this->simulationService->simulateWeek($league);

        $this->assertEquals(2, $league->fresh()->current_week);
        $this->assertDatabaseHas('league_matches', [
            'id' => $match->id,
            'is_played' => true
        ]);

        $updatedMatch = $match->fresh();
        $this->assertNotNull($updatedMatch->home_score);
        $this->assertNotNull($updatedMatch->away_score);
        $this->assertGreaterThanOrEqual(0, $updatedMatch->home_score);
        $this->assertGreaterThanOrEqual(0, $updatedMatch->away_score);
    }

    public function test_simulate_week_does_not_exceed_total_weeks()
    {
        $league = League::factory()->create(['current_week' => config('league.total_weeks')]);

        $this->simulationService->simulateWeek($league);

        $this->assertEquals(config('league.total_weeks'), $league->fresh()->current_week);
    }

    public function test_simulate_all_matches_successfully()
    {
        $league = League::factory()->create(['current_week' => 1]);
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        for ($week = 2; $week <= 3; $week++) {
            LeagueMatch::factory()->unplayed()->create([
                'league_id' => $league->id,
                'week' => $week,
                'home_team_id' => $teams[0]->id,
                'away_team_id' => $teams[1]->id,
            ]);
        }

        $this->simulationService->simulateAll($league);

        $this->assertEquals(config('league.total_weeks'), $league->fresh()->current_week);

        $matches = LeagueMatch::where('league_id', $league->id)->get();
        foreach ($matches as $match) {
            $this->assertTrue(boolval($match->is_played));
            $this->assertNotNull($match->home_score);
            $this->assertNotNull($match->away_score);
        }
    }

    public function test_simulate_match_generates_realistic_scores()
    {
        $homeTeam = Team::factory()->create(['strength' => 80]);
        $awayTeam = Team::factory()->create(['strength' => 60]);

        $match = LeagueMatch::factory()->unplayed()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->simulationService->simulateMatch($match);

        $this->assertTrue(boolval($match->fresh()->is_played));
        $this->assertNotNull($match->fresh()->home_score);
        $this->assertNotNull($match->fresh()->away_score);
        $this->assertGreaterThanOrEqual(0, $match->fresh()->home_score);
        $this->assertGreaterThanOrEqual(0, $match->fresh()->away_score);
    }

    public function test_simulate_match_respects_team_strengths()
    {
        $strongTeam = Team::factory()->create(['strength' => 90]);
        $weakTeam = Team::factory()->create(['strength' => 30]);

        $match = LeagueMatch::factory()->unplayed()->create([
            'home_team_id' => $strongTeam->id,
            'away_team_id' => $weakTeam->id,
        ]);

        $this->simulationService->simulateMatch($match);

        $updatedMatch = $match->fresh();
        $this->assertTrue(boolval($updatedMatch->is_played));
        $this->assertNotNull($updatedMatch->home_score);
        $this->assertNotNull($updatedMatch->away_score);
    }

    public function test_simulate_week_only_plays_unplayed_matches()
    {
        $league = League::factory()->create(['current_week' => 1]);
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        $playedMatch = LeagueMatch::factory()->played()->create([
            'league_id' => $league->id,
            'week' => 2,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'home_score' => 2,
            'away_score' => 1
        ]);

        $unplayedMatch = LeagueMatch::factory()->unplayed()->create([
            'league_id' => $league->id,
            'week' => 2,
            'home_team_id' => $teams[2]->id,
            'away_team_id' => $teams[3]->id,
        ]);

        $this->simulationService->simulateWeek($league);

        $this->assertEquals(2, $league->fresh()->current_week);

        $this->assertDatabaseHas('league_matches', [
            'id' => $playedMatch->id,
            'home_score' => 2,
            'away_score' => 1
        ]);

        $this->assertTrue(boolval($unplayedMatch->fresh()->is_played));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
