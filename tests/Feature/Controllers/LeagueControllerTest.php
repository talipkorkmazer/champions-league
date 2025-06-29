<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\League;
use App\Models\Team;
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
} 