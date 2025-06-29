<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\LeagueMatch;
use App\Models\League;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeagueMatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_league_match_creation()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $matchData = [
            'league_id' => $league->id,
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 2,
            'away_score' => 1,
            'is_played' => true
        ];

        $match = LeagueMatch::create($matchData);

        $this->assertInstanceOf(LeagueMatch::class, $match);
        $this->assertEquals($league->id, $match->league_id);
        $this->assertEquals(1, $match->week);
        $this->assertEquals($homeTeam->id, $match->home_team_id);
        $this->assertEquals($awayTeam->id, $match->away_team_id);
        $this->assertEquals(2, $match->home_score);
        $this->assertEquals(1, $match->away_score);
        $this->assertTrue($match->is_played);
        $this->assertDatabaseHas('league_matches', $matchData);
    }

    public function test_league_match_fillable_attributes()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $matchData = [
            'league_id' => $league->id,
            'week' => 2,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'is_played' => false
        ];

        $match = LeagueMatch::create($matchData);

        $this->assertEquals($league->id, $match->league_id);
        $this->assertEquals(2, $match->week);
        $this->assertEquals($homeTeam->id, $match->home_team_id);
        $this->assertEquals($awayTeam->id, $match->away_team_id);
        $this->assertFalse($match->is_played);
        $this->assertNull($match->home_score);
        $this->assertNull($match->away_score);
    }

    public function test_league_match_league_relationship()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id
        ]);

        $relatedLeague = $match->league;

        $this->assertInstanceOf(League::class, $relatedLeague);
        $this->assertEquals($league->id, $relatedLeague->id);
        $this->assertEquals($league->name, $relatedLeague->name);
    }

    public function test_league_match_home_team_relationship()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id
        ]);

        $relatedHomeTeam = $match->homeTeam;

        $this->assertInstanceOf(Team::class, $relatedHomeTeam);
        $this->assertEquals($homeTeam->id, $relatedHomeTeam->id);
        $this->assertEquals($homeTeam->name, $relatedHomeTeam->name);
    }

    public function test_league_match_away_team_relationship()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id
        ]);

        $relatedAwayTeam = $match->awayTeam;

        $this->assertInstanceOf(Team::class, $relatedAwayTeam);
        $this->assertEquals($awayTeam->id, $relatedAwayTeam->id);
        $this->assertEquals($awayTeam->name, $relatedAwayTeam->name);
    }

    public function test_league_match_unplayed_match()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->unplayed()->create([
            'league_id' => $league->id,
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        $this->assertFalse($match->is_played);
        $this->assertNull($match->home_score);
        $this->assertNull($match->away_score);
    }

    public function test_league_match_played_match_with_scores()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 3,
            'away_score' => 2,
            'is_played' => true
        ]);

        $this->assertTrue($match->is_played);
        $this->assertEquals(3, $match->home_score);
        $this->assertEquals(2, $match->away_score);
    }

    public function test_league_match_draw_result()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'week' => 1,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 1,
            'away_score' => 1,
            'is_played' => true
        ]);

        $this->assertTrue($match->is_played);
        $this->assertEquals(1, $match->home_score);
        $this->assertEquals(1, $match->away_score);
    }

    public function test_league_match_has_correct_table_name()
    {
        $match = new LeagueMatch();

        $this->assertEquals('league_matches', $match->getTable());
    }

    public function test_league_match_has_timestamps()
    {
        $match = LeagueMatch::factory()->create();

        $this->assertNotNull($match->created_at);
        $this->assertNotNull($match->updated_at);
    }

    public function test_league_match_all_relationships_together()
    {
        $league = League::factory()->create();
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week' => 1,
            'home_score' => 2,
            'away_score' => 0,
            'is_played' => true
        ]);

        $relatedLeague = $match->league;
        $relatedHomeTeam = $match->homeTeam;
        $relatedAwayTeam = $match->awayTeam;

        $this->assertEquals($league->id, $relatedLeague->id);
        $this->assertEquals($homeTeam->id, $relatedHomeTeam->id);
        $this->assertEquals($awayTeam->id, $relatedAwayTeam->id);
    }
}
