<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PredictionService;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\League;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PredictionService $predictionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->predictionService = new PredictionService();
    }

    public function test_add_predictions_to_standings()
    {
        $league = League::factory()->create();
        $teams = Team::factory(3)->create();

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $teams[0]->id,
            'week' => 3,
            'percentage' => 45.5
        ]);

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $teams[1]->id,
            'week' => 3,
            'percentage' => 30.2
        ]);

        $standings = [
            [
                'team' => $teams[0],
                'points' => 9,
                'played' => 3
            ],
            [
                'team' => $teams[1],
                'points' => 6,
                'played' => 3
            ],
            [
                'team' => $teams[2],
                'points' => 3,
                'played' => 3
            ]
        ];

        $standingsWithPredictions = $this->predictionService->addPredictionsToStandings(
            $standings,
            $league->id,
            3
        );

        $this->assertEquals(45.5, $standingsWithPredictions[0]['championship_percentage']);
        $this->assertEquals(30.2, $standingsWithPredictions[1]['championship_percentage']);
        $this->assertEquals(0, $standingsWithPredictions[2]['championship_percentage']); // No prediction
    }

    public function test_get_predictions_for_league_and_week()
    {
        $league = League::factory()->create();
        $teams = Team::factory(2)->create();

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $teams[0]->id,
            'week' => 4,
            'percentage' => 60.0
        ]);

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $teams[1]->id,
            'week' => 4,
            'percentage' => 40.0
        ]);

        $predictions = $this->predictionService->getPredictions($league->id, 4);

        $this->assertCount(2, $predictions);
        $this->assertEquals(60.0, $predictions[$teams[0]->id]);
        $this->assertEquals(40.0, $predictions[$teams[1]->id]);
    }

    public function test_get_predictions_returns_empty_array_when_no_predictions()
    {
        $league = League::factory()->create();

        // Act
        $predictions = $this->predictionService->getPredictions($league->id, 3);

        // Assert
        $this->assertIsArray($predictions);
        $this->assertEmpty($predictions);
    }

    public function test_get_predictions_filters_by_week()
    {
        // Arrange
        $league = League::factory()->create();
        $team = Team::factory()->create();

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 3,
            'percentage' => 50.0
        ]);

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $team->id,
            'week' => 4,
            'percentage' => 60.0
        ]);

        $predictions = $this->predictionService->getPredictions($league->id, 3);

        $this->assertCount(1, $predictions);
        $this->assertEquals(50.0, $predictions[$team->id]);
    }

    public function test_calculate_championship_percentages()
    {
        $league = League::factory()->create();
        $teams = Team::factory(2)->create();

        Prediction::factory()->create([
            'league_id' => $league->id,
            'team_id' => $teams[0]->id,
            'week' => 5,
            'percentage' => 70.0
        ]);

        $standings = [
            [
                'team' => $teams[0],
                'points' => 12
            ],
            [
                'team' => $teams[1],
                'points' => 9
            ]
        ];

        $standingsWithPercentages = $this->predictionService->calculateChampionshipPercentages(
            $standings,
            $league->id,
            5
        );

        $this->assertEquals(70.0, $standingsWithPercentages[0]['championship_percentage']);
        $this->assertEquals(0, $standingsWithPercentages[1]['championship_percentage']);
    }

    public function test_add_predictions_to_standings_with_no_predictions()
    {
        $league = League::factory()->create();
        $teams = Team::factory(2)->create();

        $standings = [
            [
                'team' => $teams[0],
                'points' => 6
            ],
            [
                'team' => $teams[1],
                'points' => 3
            ]
        ];

        $standingsWithPredictions = $this->predictionService->addPredictionsToStandings(
            $standings,
            $league->id,
            2
        );

        $this->assertEquals(0, $standingsWithPredictions[0]['championship_percentage']);
        $this->assertEquals(0, $standingsWithPredictions[1]['championship_percentage']);
    }
}
