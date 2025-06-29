<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\League;
use App\Models\Team;
use App\Models\LeagueMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeagueTest extends TestCase
{
    use RefreshDatabase;

    public function test_league_has_teams_relationship()
    {
        $league = League::factory()->create();
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        $this->assertCount(4, $league->teams);
        $this->assertInstanceOf(Team::class, $league->teams->first());
    }

    public function test_league_has_matches_relationship()
    {
        $league = League::factory()->create();
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id
        ]);

        $this->assertCount(1, $league->matches);
        $this->assertInstanceOf(LeagueMatch::class, $league->matches->first());
    }

    public function test_league_fillable_attributes()
    {
        $leagueData = [
            'name' => 'Test League',
            'current_week' => 2
        ];

        $league = League::factory()->create($leagueData);

        $this->assertEquals('Test League', $league->name);
        $this->assertEquals(2, $league->current_week);
    }

    public function test_league_can_be_created_with_factory()
    {
        $league = League::factory()->create();

        $this->assertInstanceOf(League::class, $league);
        $this->assertNotNull($league->name);
        $this->assertIsInt($league->current_week);
    }

    public function test_league_has_many_matches_with_teams()
    {
        $league = League::factory()->create();
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id
        ]);

        $league->load(['matches.homeTeam', 'matches.awayTeam']);

        $this->assertCount(1, $league->matches);
        $this->assertInstanceOf(Team::class, $league->matches->first()->homeTeam);
        $this->assertInstanceOf(Team::class, $league->matches->first()->awayTeam);
    }
}
