<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Prediction;
use App\Models\League;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PredictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_prediction_creation()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $predictionData = [
            'league_id' => $league->id,
            'week' => 3,
            'team_id' => $team->id,
            'percentage' => 45.5
        ];

        $prediction = Prediction::create($predictionData);

        $this->assertInstanceOf(Prediction::class, $prediction);
        $this->assertEquals($league->id, $prediction->league_id);
        $this->assertEquals(3, $prediction->week);
        $this->assertEquals($team->id, $prediction->team_id);
        $this->assertEquals(45.5, $prediction->percentage);
        $this->assertDatabaseHas('predictions', $predictionData);
    }

    public function test_prediction_fillable_attributes()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $predictionData = [
            'league_id' => $league->id,
            'week' => 5,
            'team_id' => $team->id,
            'percentage' => 75.2
        ];

        $prediction = Prediction::create($predictionData);

        $this->assertEquals($league->id, $prediction->league_id);
        $this->assertEquals(5, $prediction->week);
        $this->assertEquals($team->id, $prediction->team_id);
        $this->assertEquals(75.2, $prediction->percentage);
    }

    public function test_prediction_league_relationship()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 1,
            'percentage' => 50.0
        ]);

        $relatedLeague = $prediction->league;

        $this->assertInstanceOf(League::class, $relatedLeague);
        $this->assertEquals($league->id, $relatedLeague->id);
        $this->assertEquals($league->name, $relatedLeague->name);
    }

    public function test_prediction_team_relationship()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 1,
            'percentage' => 60.0
        ]);

        $relatedTeam = $prediction->team;

        $this->assertInstanceOf(Team::class, $relatedTeam);
        $this->assertEquals($team->id, $relatedTeam->id);
        $this->assertEquals($team->name, $relatedTeam->name);
    }

    public function test_prediction_with_high_percentage()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'week' => 1,
            'team_id' => $team->id,
            'percentage' => 95.8
        ]);

        $this->assertEquals(95.8, $prediction->percentage);
    }

    public function test_prediction_with_low_percentage()
    {
        // Arrange
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'week' => 1,
            'team_id' => $team->id,
            'percentage' => 5.2
        ]);

        // Assert
        $this->assertEquals(5.2, $prediction->percentage);
    }

    public function test_prediction_with_zero_percentage()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'week' => 1,
            'team_id' => $team->id,
            'percentage' => 0.0
        ]);

        $this->assertEquals(0.0, $prediction->percentage);
    }

    public function test_prediction_with_100_percentage()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'week' => 1,
            'team_id' => $team->id,
            'percentage' => 100.0
        ]);

        $this->assertEquals(100.0, $prediction->percentage);
    }

    public function test_prediction_has_correct_table_name()
    {
        $prediction = new Prediction();

        $this->assertEquals('predictions', $prediction->getTable());
    }

    public function test_prediction_has_timestamps()
    {
        $prediction = Prediction::factory()->create();

        $this->assertNotNull($prediction->created_at);
        $this->assertNotNull($prediction->updated_at);
    }

    public function test_prediction_all_relationships_together()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 2,
            'percentage' => 80.5
        ]);

        $relatedLeague = $prediction->league;
        $relatedTeam = $prediction->team;

        $this->assertEquals($league->id, $relatedLeague->id);
        $this->assertEquals($team->id, $relatedTeam->id);
        $this->assertEquals(2, $prediction->week);
        $this->assertEquals(80.5, $prediction->percentage);
    }

    public function test_multiple_predictions_for_same_team()
    {
        $league = League::factory()->create();
        $team = Team::factory()->create();

        $prediction1 = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 1,
            'percentage' => 70.0
        ]);

        $prediction2 = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 2,
            'percentage' => 65.0
        ]);

        $teamPredictions = $team->predictions;

        $this->assertCount(2, $teamPredictions);
        $this->assertEquals(70.0, $prediction1->percentage);
        $this->assertEquals(65.0, $prediction2->percentage);
    }

    public function test_predictions_for_different_teams_same_week()
    {
        $league = League::factory()->create();
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $prediction1 = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team1->id,
            'week' => 1,
            'percentage' => 60.0
        ]);

        $prediction2 = Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team2->id,
            'week' => 1,
            'percentage' => 40.0
        ]);

        $leaguePredictions = $league->predictions;

        $this->assertCount(2, $leaguePredictions);
        $this->assertEquals(60.0, $prediction1->percentage);
        $this->assertEquals(40.0, $prediction2->percentage);
    }
}
