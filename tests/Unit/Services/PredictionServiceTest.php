<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PredictionService;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\League;
use App\Services\StandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;


class PredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PredictionService $predictionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->predictionService = new PredictionService(app(StandingService::class));
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

    private function makeTeam($id, $name = 'Team', $strength = 80)
    {
        $team = new Team();
        $team->id = $id;
        $team->name = $name;
        $team->strength = $strength;
        return $team;
    }

    private function makeStanding($team, $points, $goal_difference = 0, $goals_for = 0, $goals_against = 0)
    {
        return [
            'team' => $team,
            'points' => $points,
            'goal_difference' => $goal_difference,
            'goals_for' => $goals_for,
            'goals_against' => $goals_against,
        ];
    }

    private function mockStandingService($standings)
    {
        $mock = Mockery::mock(StandingService::class);
        $mock->shouldReceive('calculateStandings')->andReturn($standings);
        $mock->shouldReceive('sortStandings')->andReturn($standings);
        $this->app->instance(StandingService::class, $mock);
        return $mock;
    }

    public function test_league_not_started_no_predictions()
    {
        $league = League::factory()->create(['current_week' => 1]);
        $service = new PredictionService(app(StandingService::class));
        $this->assertNull($service->calculatePredictions($league));
        $this->assertEquals(0, Prediction::count());
    }

    public function test_mid_league_all_teams_in_race()
    {
        $league = League::factory()->create(['current_week' => 4]);
        $team1 = Team::factory()->create(['name' => 'A']);
        $team2 = Team::factory()->create(['name' => 'B']);
        $standings = [
            $this->makeStanding($team1, 6, 2, 5, 3),
            $this->makeStanding($team2, 5, 1, 4, 3),
        ];
        $this->mockStandingService($standings);
        $league->setRelation('teams', collect([$team1, $team2]));
        $service = new PredictionService(app(StandingService::class));
        $service->calculatePredictions($league);
        $preds = Prediction::all()->keyBy('team_id');
        $this->assertCount(2, $preds);
        $this->assertTrue($preds[$team1->id]->percentage > $preds[$team2->id]->percentage);
        $this->assertEquals(100, round($preds[$team1->id]->percentage + $preds[$team2->id]->percentage, 2));
    }

    public function test_teams_mathematically_eliminated()
    {
        $league = League::factory()->create(['current_week' => 5]);
        $team1 = Team::factory()->create(['name' => 'A']);
        $team2 = Team::factory()->create(['name' => 'B']);
        $team3 = Team::factory()->create(['name' => 'C']);
        $standings = [
            $this->makeStanding($team1, 10, 3, 8, 5),
            $this->makeStanding($team2, 10, 2, 6, 4),
            $this->makeStanding($team3, 2, -4, 2, 6),
        ];
        $this->mockStandingService($standings);
        $league->setRelation('teams', collect([$team1, $team2, $team3]));
        $service = new PredictionService(app(StandingService::class));
        $service->calculatePredictions($league);
        $preds = Prediction::all()->keyBy('team_id');
        $this->assertEquals(0.0, $preds[$team3->id]->percentage);
        $this->assertEquals(100, round($preds[$team1->id]->percentage + $preds[$team2->id]->percentage, 2));
    }

    public function test_league_finished_clear_winner()
    {
        $league = League::factory()->create(['current_week' => 6]);
        $team1 = Team::factory()->create(['name' => 'A']);
        $team2 = Team::factory()->create(['name' => 'B']);
        $standings = [
            $this->makeStanding($team1, 12, 5, 10, 5),
            $this->makeStanding($team2, 9, 2, 7, 5),
        ];
        $this->mockStandingService($standings);
        $league->setRelation('teams', collect([$team1, $team2]));
        $service = new PredictionService(app(StandingService::class));
        $service->calculatePredictions($league);
        $preds = Prediction::all()->keyBy('team_id');
        $this->assertEquals(100.0, $preds[$team1->id]->percentage);
        $this->assertEquals(0.0, $preds[$team2->id]->percentage);
    }

    public function test_league_finished_tie()
    {
        $league = League::factory()->create(['current_week' => 6]);
        $team1 = Team::factory()->create(['name' => 'A']);
        $team2 = Team::factory()->create(['name' => 'B']);
        $standings = [
            $this->makeStanding($team1, 10, 2, 8, 6),
            $this->makeStanding($team2, 10, 2, 8, 6),
        ];
        $this->mockStandingService($standings);
        $league->setRelation('teams', collect([$team1, $team2]));
        $service = new PredictionService(app(StandingService::class));
        $service->calculatePredictions($league);
        $preds = Prediction::all()->keyBy('team_id');
        $this->assertEquals(100.0, $preds[$team1->id]->percentage);
        $this->assertEquals(100.0, $preds[$team2->id]->percentage);
    }

    public function test_only_one_team_in_race()
    {
        $league = League::factory()->create(['current_week' => 5]);
        $team1 = Team::factory()->create(['name' => 'A']);
        $team2 = Team::factory()->create(['name' => 'B']);
        $standings = [
            $this->makeStanding($team1, 15, 5, 12, 7),
            $this->makeStanding($team2, 8, -2, 6, 8),
        ];
        $this->mockStandingService($standings);
        $league->setRelation('teams', collect([$team1, $team2]));
        $service = new PredictionService(app(StandingService::class));
        $service->calculatePredictions($league);
        $preds = Prediction::all()->keyBy('team_id');
        $this->assertEquals(100.0, $preds[$team1->id]->percentage);
        $this->assertEquals(0.0, $preds[$team2->id]->percentage);
    }

    public function test_all_teams_in_race_equal_points()
    {
        $league = League::factory()->create(['current_week' => 4]);
        $team1 = Team::factory()->create(['name' => 'A']);
        $team2 = Team::factory()->create(['name' => 'B']);
        $team3 = Team::factory()->create(['name' => 'C']);
        $standings = [
            $this->makeStanding($team1, 5, 1, 4, 3),
            $this->makeStanding($team2, 5, 0, 3, 3),
            $this->makeStanding($team3, 5, -1, 2, 3),
        ];
        $this->mockStandingService($standings);
        $league->setRelation('teams', collect([$team1, $team2, $team3]));
        $service = new PredictionService(app(StandingService::class));
        $service->calculatePredictions($league);
        $preds = Prediction::all()->keyBy('team_id');
        $sum = round($preds[$team1->id]->percentage + $preds[$team2->id]->percentage + $preds[$team3->id]->percentage, 2);
        $this->assertTrue(abs(100 - $sum) < 0.05);
        $this->assertTrue($preds[$team1->id]->percentage > $preds[$team2->id]->percentage);
        $this->assertTrue($preds[$team2->id]->percentage > $preds[$team3->id]->percentage);
    }
}
