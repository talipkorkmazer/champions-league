<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\StandingService;
use App\Models\League;
use App\Models\Team;
use App\Models\LeagueMatch;
use App\DTOs\TeamStatsDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StandingServiceTest extends TestCase
{
    use RefreshDatabase;

    private StandingService $standingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->standingService = new StandingService();
    }

    public function test_calculate_standings_successfully()
    {
        $league = League::factory()->create();
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'home_score' => 2,
            'away_score' => 1,
            'is_played' => true
        ]);

        $standings = $this->standingService->calculateStandings($league);

        $this->assertIsArray($standings);
        $this->assertCount(4, $standings);

        $this->assertGreaterThanOrEqual($standings[1]['points'], $standings[0]['points']);
    }

    public function test_calculate_team_stats_successfully()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $league->teams()->attach($team->id);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $team->id,
            'away_team_id' => Team::factory()->create()->id,
            'home_score' => 3,
            'away_score' => 1,
            'is_played' => true
        ]);

        $teamStats = $this->standingService->calculateTeamStats($team, $league);

        $this->assertInstanceOf(TeamStatsDTO::class, $teamStats);
        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(1, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(3, $teamStats->goalsFor);
        $this->assertEquals(1, $teamStats->goalsAgainst);
        $this->assertEquals(2, $teamStats->goalDifference);
        $this->assertEquals(config('league.points_for_win'), $teamStats->points);
    }

    public function test_sort_standings_by_points_goal_difference_and_goals_scored()
    {
        $standings = [
            [
                'team' => ['id' => 1, 'name' => 'Team A'],
                'points' => 6,
                'goal_difference' => 2,
                'goals_for' => 5,
                'goals_against' => 3,
                'played' => 2,
                'won' => 2,
                'drawn' => 0,
                'lost' => 0
            ],
            [
                'team' => ['id' => 2, 'name' => 'Team B'],
                'points' => 6,
                'goal_difference' => 2,
                'goals_for' => 4,
                'goals_against' => 2,
                'played' => 2,
                'won' => 2,
                'drawn' => 0,
                'lost' => 0
            ],
            [
                'team' => ['id' => 3, 'name' => 'Team C'],
                'points' => 3,
                'goal_difference' => 0,
                'goals_for' => 2,
                'goals_against' => 2,
                'played' => 2,
                'won' => 1,
                'drawn' => 0,
                'lost' => 1
            ]
        ];

        $sortedStandings = $this->standingService->sortStandings($standings);

        $this->assertEquals('Team A', $sortedStandings[0]['team']['name']); // Higher goals scored
        $this->assertEquals('Team B', $sortedStandings[1]['team']['name']);
        $this->assertEquals('Team C', $sortedStandings[2]['team']['name']); // Lower points
    }

    public function test_calculate_team_stats_with_draw()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $league->teams()->attach($team->id);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $team->id,
            'away_team_id' => Team::factory()->create()->id,
            'home_score' => 1,
            'away_score' => 1,
            'is_played' => true
        ]);

        $teamStats = $this->standingService->calculateTeamStats($team, $league);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(1, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(config('league.points_for_draw'), $teamStats->points);
    }

    public function test_calculate_team_stats_with_loss()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $league->teams()->attach($team->id);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $team->id,
            'away_team_id' => Team::factory()->create()->id,
            'home_score' => 0,
            'away_score' => 2,
            'is_played' => true
        ]);

        $teamStats = $this->standingService->calculateTeamStats($team, $league);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(1, $teamStats->lost);
        $this->assertEquals(0, $teamStats->points);
    }

    public function test_calculate_team_stats_ignores_unplayed_matches()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $league->teams()->attach($team->id);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $team->id,
            'away_team_id' => Team::factory()->create()->id,
            'home_score' => null,
            'away_score' => null,
            'is_played' => false
        ]);

        $teamStats = $this->standingService->calculateTeamStats($team, $league);

        $this->assertEquals(0, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(0, $teamStats->points);
    }

    public function test_calculate_team_stats_with_away_matches()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();
        $league->teams()->attach($team->id);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => Team::factory()->create()->id,
            'away_team_id' => $team->id,
            'home_score' => 1,
            'away_score' => 2,
            'is_played' => true
        ]);

        $teamStats = $this->standingService->calculateTeamStats($team, $league);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(1, $teamStats->won);
        $this->assertEquals(2, $teamStats->goalsFor);
        $this->assertEquals(1, $teamStats->goalsAgainst);
        $this->assertEquals(1, $teamStats->goalDifference);
    }
}
