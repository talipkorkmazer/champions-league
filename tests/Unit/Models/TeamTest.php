<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Team;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\LeagueMatch;
use App\Models\Prediction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_creation()
    {
        $teamData = [
            'name' => 'Manchester United',
            'strength' => 85
        ];

        $team = Team::factory()->create($teamData);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('Manchester United', $team->name);
        $this->assertEquals(85, $team->strength);
        $this->assertDatabaseHas('teams', $teamData);
    }

    public function test_team_fillable_attributes()
    {
        $teamData = [
            'name' => 'Liverpool',
            'strength' => 88
        ];

        $team = Team::factory()->create($teamData);

        $this->assertEquals('Liverpool', $team->name);
        $this->assertEquals(88, $team->strength);
    }

    public function test_team_league_teams_relationship()
    {
        $team = Team::factory()->create();
        $league = League::factory()->create();

        LeagueTeam::factory()->create([
            'team_id' => $team->id,
            'league_id' => $league->id
        ]);

        $leagueTeams = $team->leagueTeams;

        $this->assertCount(1, $leagueTeams);
        $this->assertInstanceOf(LeagueTeam::class, $leagueTeams->first());
        $this->assertEquals($team->id, $leagueTeams->first()->team_id);
        $this->assertEquals($league->id, $leagueTeams->first()->league_id);
    }

    public function test_team_matches_as_home_relationship()
    {
        $team = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $league = League::factory()->create();

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $team->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1
        ]);

        $homeMatches = $team->matchesAsHome;

        $this->assertCount(1, $homeMatches);
        $this->assertInstanceOf(LeagueMatch::class, $homeMatches->first());
        $this->assertEquals($team->id, $homeMatches->first()->home_team_id);
        $this->assertEquals($awayTeam->id, $homeMatches->first()->away_team_id);
    }

    public function test_team_matches_as_away_relationship()
    {
        $team = Team::factory()->create();
        $homeTeam = Team::factory()->create();
        $league = League::factory()->create();

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $team->id,
            'week' => 1
        ]);

        $awayMatches = $team->matchesAsAway;

        $this->assertCount(1, $awayMatches);
        $this->assertInstanceOf(LeagueMatch::class, $awayMatches->first());
        $this->assertEquals($homeTeam->id, $awayMatches->first()->home_team_id);
        $this->assertEquals($team->id, $awayMatches->first()->away_team_id);
    }

    public function test_team_predictions_relationship()
    {
        $team = Team::factory()->create();
        $league = League::factory()->create();

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 1,
            'percentage' => 45.5
        ]);

        $predictions = $team->predictions;

        $this->assertCount(1, $predictions);
        $this->assertInstanceOf(Prediction::class, $predictions->first());
        $this->assertEquals($team->id, $predictions->first()->team_id);
        $this->assertEquals(45.5, $predictions->first()->percentage);
    }

    public function test_team_multiple_relationships()
    {
        $team = Team::factory()->create();
        $otherTeam = Team::factory()->create();
        $league = League::factory()->create();

        LeagueTeam::factory()->create([
            'team_id' => $team->id,
            'league_id' => $league->id
        ]);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $team->id,
            'away_team_id' => $otherTeam->id,
            'week' => 1
        ]);

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $otherTeam->id,
            'away_team_id' => $team->id,
            'week' => 2
        ]);

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 1,
            'percentage' => 60.0
        ]);

        $leagueTeams = $team->leagueTeams;
        $homeMatches = $team->matchesAsHome;
        $awayMatches = $team->matchesAsAway;
        $predictions = $team->predictions;

        $this->assertCount(1, $leagueTeams);
        $this->assertCount(1, $homeMatches);
        $this->assertCount(1, $awayMatches);
        $this->assertCount(1, $predictions);
    }

    public function test_team_has_correct_table_name()
    {
        $team = new Team();

        $this->assertEquals('teams', $team->getTable());
    }

    public function test_team_has_timestamps()
    {
        $team = Team::factory()->create();

        $this->assertNotNull($team->created_at);
        $this->assertNotNull($team->updated_at);
    }
}
