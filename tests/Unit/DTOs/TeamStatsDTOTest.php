<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\TeamStatsDTO;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamStatsDTOTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_stats_dto_creation()
    {
        $team = Team::factory()->create();

        $teamStats = new TeamStatsDTO($team);

        $this->assertInstanceOf(TeamStatsDTO::class, $teamStats);
        $this->assertEquals($team, $teamStats->team);
        $this->assertEquals(0, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(0, $teamStats->goalsFor);
        $this->assertEquals(0, $teamStats->goalsAgainst);
        $this->assertEquals(0, $teamStats->points);
        $this->assertEquals(0, $teamStats->goalDifference);
        $this->assertEquals(0.0, $teamStats->championshipPercentage);
    }

    public function test_team_stats_dto_creation_with_initial_values()
    {
        $team = Team::factory()->create();

        $teamStats = new TeamStatsDTO(
            team: $team,
            played: 5,
            won: 3,
            drawn: 1,
            lost: 1,
            goalsFor: 10,
            goalsAgainst: 5,
            points: 10,
            goalDifference: 5,
            championshipPercentage: 75.5
        );

        $this->assertEquals(5, $teamStats->played);
        $this->assertEquals(3, $teamStats->won);
        $this->assertEquals(1, $teamStats->drawn);
        $this->assertEquals(1, $teamStats->lost);
        $this->assertEquals(10, $teamStats->goalsFor);
        $this->assertEquals(5, $teamStats->goalsAgainst);
        $this->assertEquals(10, $teamStats->points);
        $this->assertEquals(5, $teamStats->goalDifference);
        $this->assertEquals(75.5, $teamStats->championshipPercentage);
    }

    public function test_calculate_points()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team, played: 3, won: 2, drawn: 1, lost: 0);

        $teamStats->calculatePoints(3, 1);

        $this->assertEquals(7, $teamStats->points);
    }

    public function test_calculate_points_with_custom_values()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team, played: 2, won: 1, drawn: 1, lost: 0);

        $teamStats->calculatePoints(2, 1);

        $this->assertEquals(3, $teamStats->points);
    }

    public function test_calculate_goal_difference()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team, goalsFor: 15, goalsAgainst: 8);

        $teamStats->calculateGoalDifference();

        $this->assertEquals(7, $teamStats->goalDifference);
    }

    public function test_calculate_goal_difference_negative()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team, goalsFor: 5, goalsAgainst: 12);

        $teamStats->calculateGoalDifference();

        $this->assertEquals(-7, $teamStats->goalDifference);
    }

    public function test_add_match_result_home_win()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team);

        $teamStats->addMatchResult(3, 1);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(1, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(3, $teamStats->goalsFor);
        $this->assertEquals(1, $teamStats->goalsAgainst);
    }

    public function test_add_match_result_home_draw()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team);

        $teamStats->addMatchResult(2, 2);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(1, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(2, $teamStats->goalsFor);
        $this->assertEquals(2, $teamStats->goalsAgainst);
    }

    public function test_add_match_result_home_loss()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team);

        $teamStats->addMatchResult(1, 3);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(1, $teamStats->lost);
        $this->assertEquals(1, $teamStats->goalsFor);
        $this->assertEquals(3, $teamStats->goalsAgainst);
    }

    public function test_add_match_result_away_win()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team);

        $teamStats->addMatchResult(2, 1);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(1, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(0, $teamStats->lost);
        $this->assertEquals(2, $teamStats->goalsFor);
        $this->assertEquals(1, $teamStats->goalsAgainst);
    }

    public function test_add_match_result_away_loss()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team);

        $teamStats->addMatchResult(0, 2);

        $this->assertEquals(1, $teamStats->played);
        $this->assertEquals(0, $teamStats->won);
        $this->assertEquals(0, $teamStats->drawn);
        $this->assertEquals(1, $teamStats->lost);
        $this->assertEquals(0, $teamStats->goalsFor);
        $this->assertEquals(2, $teamStats->goalsAgainst);
    }

    public function test_add_multiple_match_results()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team);

        $teamStats->addMatchResult(2, 1);
        $teamStats->addMatchResult(1, 1);
        $teamStats->addMatchResult(0, 2);

        $this->assertEquals(3, $teamStats->played);
        $this->assertEquals(1, $teamStats->won);
        $this->assertEquals(1, $teamStats->drawn);
        $this->assertEquals(1, $teamStats->lost);
        $this->assertEquals(3, $teamStats->goalsFor);
        $this->assertEquals(4, $teamStats->goalsAgainst);
    }

    public function test_to_array_conversion()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO(
            team: $team,
            played: 3,
            won: 2,
            drawn: 1,
            lost: 0,
            goalsFor: 8,
            goalsAgainst: 3,
            points: 7,
            goalDifference: 5,
            championshipPercentage: 60.5
        );

        $array = $teamStats->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($team, $array['team']);
        $this->assertEquals(3, $array['played']);
        $this->assertEquals(2, $array['won']);
        $this->assertEquals(1, $array['drawn']);
        $this->assertEquals(0, $array['lost']);
        $this->assertEquals(8, $array['goals_for']);
        $this->assertEquals(3, $array['goals_against']);
        $this->assertEquals(5, $array['goal_difference']);
        $this->assertEquals(7, $array['points']);
        $this->assertEquals(60.5, $array['championship_percentage']);
    }

    public function test_calculate_points_and_goal_difference_together()
    {
        $team = Team::factory()->create();
        $teamStats = new TeamStatsDTO($team, won: 2, drawn: 1, lost: 1, goalsFor: 10, goalsAgainst: 6);

        $teamStats->calculatePoints();
        $teamStats->calculateGoalDifference();

        $this->assertEquals(7, $teamStats->points);
        $this->assertEquals(4, $teamStats->goalDifference);
    }
}
