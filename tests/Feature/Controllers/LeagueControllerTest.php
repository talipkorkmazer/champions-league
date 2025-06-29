<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\League;
use App\Models\Team;
use App\Models\LeagueMatch;
use App\Models\Prediction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeagueControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_leagues()
    {
        // Arrange
        $leagues = League::factory(3)->create();
        $teams = Team::factory(4)->create();
        
        foreach ($leagues as $league) {
            $league->teams()->attach($teams->pluck('id'));
        }

        // Act
        $response = $this->get('/leagues');

        // Assert
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Leagues/Index')
            ->has('leagues', 3)
        );
    }

    public function test_create_displays_teams()
    {
        // Arrange
        $teams = Team::factory(8)->create();

        // Act
        $response = $this->get('/leagues/create');

        // Assert
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Leagues/Create')
            ->has('teams', 8)
            ->has('leagueConfig')
        );
    }

    public function test_store_creates_league()
    {
        // Arrange
        $teams = Team::factory(4)->create();
        $leagueData = [
            'name' => 'Test League',
            'team_ids' => $teams->pluck('id')->toArray()
        ];

        // Act
        $response = $this->post('/leagues', $leagueData);

        // Assert
        $response->assertRedirect('/leagues');
        $this->assertDatabaseHas('leagues', ['name' => 'Test League']);
        $this->assertDatabaseHas('league_teams', [
            'league_id' => 1,
            'team_id' => $teams->first()->id
        ]);
    }

    public function test_show_displays_league()
    {
        // Arrange
        $league = League::factory()->create();
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        // Act
        $response = $this->get("/leagues/{$league->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Leagues/Show')
            ->has('league')
            ->has('standings')
            ->has('leagueUtility')
        );
    }

    public function test_reset_league()
    {
        // Arrange
        $league = League::factory()->create(['current_week' => 3]);
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        // Act
        $response = $this->post("/leagues/{$league->id}/reset");

        // Assert
        $response->assertRedirect();
        $this->assertEquals(0, $league->fresh()->current_week);
    }

    public function test_can_update_match_result()
    {
        $league = League::factory()->create(['current_week' => 2]);
        $teams = Team::factory(2)->create();
        $league->teams()->attach($teams->pluck('id'));

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'home_score' => 1,
            'away_score' => 0,
            'is_played' => true,
            'week' => 1
        ]);

        $response = $this->patch(route('leagues.matches.update', [
            'league' => $league->id,
            'leagueMatch' => $match->id
        ]), [
            'home_score' => 3,
            'away_score' => 1
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $match->refresh();
        $this->assertEquals(3, $match->home_score);
        $this->assertEquals(1, $match->away_score);
        $this->assertTrue(boolval($match->is_played));
    }

    public function test_cannot_update_match_with_invalid_scores()
    {
        $league = League::factory()->create();
        $teams = Team::factory(2)->create();
        $league->teams()->attach($teams->pluck('id'));

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'home_score' => 1,
            'away_score' => 0,
            'is_played' => true
        ]);

        $response = $this->patch(route('leagues.matches.update', [
            'league' => $league->id,
            'leagueMatch' => $match->id
        ]), [
            'home_score' => -1,
            'away_score' => 2
        ]);

        $response->assertSessionHasErrors(['home_score']);
    }

    public function test_updating_match_result_recalculates_predictions()
    {
        $league = League::factory()->create(['current_week' => 4]);
        $teams = Team::factory(4)->create();
        $league->teams()->attach($teams->pluck('id'));

        // Create some played matches
        LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'home_score' => 2,
            'away_score' => 1,
            'is_played' => true,
            'week' => 1
        ]);

        $match = LeagueMatch::factory()->create([
            'league_id' => $league->id,
            'home_team_id' => $teams[2]->id,
            'away_team_id' => $teams[3]->id,
            'home_score' => 1,
            'away_score' => 1,
            'is_played' => true,
            'week' => 2
        ]);

        // Update the match result
        $this->patch(route('leagues.matches.update', [
            'league' => $league->id,
            'leagueMatch' => $match->id
        ]), [
            'home_score' => 3,
            'away_score' => 0
        ]);

        // Check that predictions were recalculated
        $predictions = Prediction::where('league_id', $league->id)->get();
        $this->assertGreaterThan(0, $predictions->count());
    }
} 