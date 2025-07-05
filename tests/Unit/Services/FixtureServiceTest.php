<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\FixtureService;
use App\Models\League;
use App\DTOs\MatchDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixtureServiceTest extends TestCase
{
    use RefreshDatabase;

    private FixtureService $fixtureService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtureService = new FixtureService();
    }

    public function test_generate_fixtures_for_league()
    {
        $league = League::factory()->create();
        $teamIds = [1, 2, 3, 4];

        $fixtures = $this->fixtureService->generateFixtures($league, $teamIds);

        $this->assertIsArray($fixtures);
        $this->assertNotEmpty($fixtures);

        foreach ($fixtures as $fixture) {
            $this->assertArrayHasKey('league_id', $fixture);
            $this->assertArrayHasKey('week', $fixture);
            $this->assertArrayHasKey('home_team_id', $fixture);
            $this->assertArrayHasKey('away_team_id', $fixture);
            $this->assertArrayHasKey('is_played', $fixture);
            $this->assertEquals($league->id, $fixture['league_id']);
        }
    }

    public function test_generate_all_possible_matches()
    {
        $teamIds = [1, 2, 3];

        $matches = $this->fixtureService->generateAllPossibleMatches($teamIds);

        $this->assertIsArray($matches);
        $this->assertCount(6, $matches);

        foreach ($matches as $match) {
            $this->assertArrayHasKey('home_team_id', $match);
            $this->assertArrayHasKey('away_team_id', $match);
            $this->assertNotEquals($match['home_team_id'], $match['away_team_id']);
        }
    }

    public function test_generate_all_possible_matches_with_four_teams()
    {
        $teamIds = [1, 2, 3, 4];

        $matches = $this->fixtureService->generateAllPossibleMatches($teamIds);

        $this->assertIsArray($matches);
        $this->assertCount(12, $matches);

        $matchCombinations = [
            ['home' => 1, 'away' => 2],
            ['home' => 1, 'away' => 3],
            ['home' => 1, 'away' => 4],
            ['home' => 2, 'away' => 1],
            ['home' => 2, 'away' => 3],
            ['home' => 2, 'away' => 4],
            ['home' => 3, 'away' => 1],
            ['home' => 3, 'away' => 2],
            ['home' => 3, 'away' => 4],
            ['home' => 4, 'away' => 1],
            ['home' => 4, 'away' => 2],
            ['home' => 4, 'away' => 3],
        ];

        foreach ($matchCombinations as $combination) {
            $found = false;
            foreach ($matches as $match) {
                if (
                    $match['home_team_id'] === $combination['home'] &&
                    $match['away_team_id'] === $combination['away']
                ) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, "Match combination {$combination['home']} vs {$combination['away']} not found");
        }
    }

    public function test_create_match_dto()
    {
        $homeTeamId = 1;
        $awayTeamId = 2;
        $week = 3;

        $match = $this->fixtureService->createMatch($homeTeamId, $awayTeamId, $week);

        $this->assertInstanceOf(MatchDTO::class, $match);
        $this->assertEquals($homeTeamId, $match->homeTeamId);
        $this->assertEquals($awayTeamId, $match->awayTeamId);
        $this->assertEquals($week, $match->week);
        $this->assertFalse($match->isPlayed);
        $this->assertNull($match->homeScore);
        $this->assertNull($match->awayScore);
    }

    public function test_generate_fixtures_distributes_matches_across_weeks()
    {
        $league = League::factory()->create();
        $teamIds = [1, 2, 3, 4];

        $fixtures = $this->fixtureService->generateFixtures($league, $teamIds);

        $totalWeeks = config('league.total_weeks');
        $matchesPerWeek = config('league.matches_per_week');

        $fixturesByWeek = [];
        foreach ($fixtures as $fixture) {
            $week = $fixture['week'];
            if (!isset($fixturesByWeek[$week])) {
                $fixturesByWeek[$week] = [];
            }
            $fixturesByWeek[$week][] = $fixture;
        }

        foreach ($fixturesByWeek as $week => $weekFixtures) {
            $this->assertLessThanOrEqual(
                $matchesPerWeek,
                count($weekFixtures),
                "Week $week has more than $matchesPerWeek matches"
            );
        }

        for ($week = 1; $week <= $totalWeeks; $week++) {
            $this->assertArrayHasKey($week, $fixturesByWeek, "Week $week is missing");
        }
    }

    public function test_generate_fixtures_creates_unique_matches()
    {
        $league = League::factory()->create();
        $teamIds = [1, 2, 3, 4];

        $fixtures = $this->fixtureService->generateFixtures($league, $teamIds);

        $matchKeys = [];
        foreach ($fixtures as $fixture) {
            $key = $fixture['home_team_id'] . '-' . $fixture['away_team_id'] . '-' . $fixture['week'];
            $this->assertNotContains($key, $matchKeys, "Duplicate match found: $key");
            $matchKeys[] = $key;
        }
    }

    public function test_generate_fixtures_sets_correct_league_id()
    {
        $league = League::factory()->create();
        $teamIds = [1, 2, 3, 4];

        $fixtures = $this->fixtureService->generateFixtures($league, $teamIds);

        foreach ($fixtures as $fixture) {
            $this->assertEquals($league->id, $fixture['league_id']);
        }
    }

    public function test_one_team_can_only_have_one_match_in_same_week()
    {
        $league = League::factory()->create();
        $teamIds = [1, 2, 3, 4];

        $fixtures = $this->fixtureService->generateFixtures($league, $teamIds);

        // Group fixtures by week
        $fixturesByWeek = [];
        foreach ($fixtures as $fixture) {
            $week = $fixture['week'];
            if (!isset($fixturesByWeek[$week])) {
                $fixturesByWeek[$week] = [];
            }
            $fixturesByWeek[$week][] = $fixture;
        }

        // Check each week to ensure no team appears more than once
        foreach ($fixturesByWeek as $week => $weekFixtures) {
            $teamsInWeek = [];

            foreach ($weekFixtures as $fixture) {
                $homeTeamId = $fixture['home_team_id'];
                $awayTeamId = $fixture['away_team_id'];

                // Check if home team already has a match this week
                $this->assertNotContains(
                    $homeTeamId,
                    $teamsInWeek,
                    "Team $homeTeamId appears more than once in week $week"
                );

                // Check if away team already has a match this week
                $this->assertNotContains(
                    $awayTeamId,
                    $teamsInWeek,
                    "Team $awayTeamId appears more than once in week $week"
                );

                // Add both teams to the list for this week
                $teamsInWeek[] = $homeTeamId;
                $teamsInWeek[] = $awayTeamId;
            }
        }
    }
}
