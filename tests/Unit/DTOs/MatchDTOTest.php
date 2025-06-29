<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\MatchDTO;

class MatchDTOTest extends TestCase
{
    public function test_match_dto_creation()
    {
        // Arrange
        $homeTeamId = 1;
        $awayTeamId = 2;
        $week = 3;

        // Act
        $match = new MatchDTO($homeTeamId, $awayTeamId, $week);

        // Assert
        $this->assertInstanceOf(MatchDTO::class, $match);
        $this->assertEquals($homeTeamId, $match->homeTeamId);
        $this->assertEquals($awayTeamId, $match->awayTeamId);
        $this->assertEquals($week, $match->week);
        $this->assertFalse($match->isPlayed);
        $this->assertNull($match->homeScore);
        $this->assertNull($match->awayScore);
    }

    public function test_match_dto_creation_with_scores()
    {
        $homeTeamId = 1;
        $awayTeamId = 2;
        $week = 3;
        $homeScore = 2;
        $awayScore = 1;

        $match = new MatchDTO($homeTeamId, $awayTeamId, $week, true, $homeScore, $awayScore);

        $this->assertEquals($homeTeamId, $match->homeTeamId);
        $this->assertEquals($awayTeamId, $match->awayTeamId);
        $this->assertEquals($week, $match->week);
        $this->assertTrue($match->isPlayed);
        $this->assertEquals($homeScore, $match->homeScore);
        $this->assertEquals($awayScore, $match->awayScore);
    }

    public function test_is_home_match()
    {
        $match = new MatchDTO(1, 2, 1);

        $this->assertTrue($match->isHomeMatch(1));
        $this->assertFalse($match->isHomeMatch(2));
        $this->assertFalse($match->isHomeMatch(3));
    }

    public function test_is_away_match()
    {
        $match = new MatchDTO(1, 2, 1);

        $this->assertFalse($match->isAwayMatch(1));
        $this->assertTrue($match->isAwayMatch(2));
        $this->assertFalse($match->isAwayMatch(3));
    }

    public function test_get_team_score_home_team()
    {
        $match = new MatchDTO(1, 2, 1, true, 3, 1);

        $score = $match->getTeamScore(1);

        $this->assertEquals(3, $score);
    }

    public function test_get_team_score_away_team()
    {
        $match = new MatchDTO(1, 2, 1, true, 3, 1);

        $score = $match->getTeamScore(2);

        $this->assertEquals(1, $score);
    }

    public function test_get_team_score_team_not_in_match()
    {
        $match = new MatchDTO(1, 2, 1, true, 3, 1);

        $score = $match->getTeamScore(3);

        $this->assertNull($score);
    }

    public function test_get_team_score_unplayed_match()
    {
        $match = new MatchDTO(1, 2, 1, false);

        $score = $match->getTeamScore(1);

        $this->assertNull($score);
    }

    public function test_get_opponent_score_home_team()
    {
        $match = new MatchDTO(1, 2, 1, true, 3, 1);

        $score = $match->getOpponentScore(1);

        $this->assertEquals(1, $score);
    }

    public function test_get_opponent_score_away_team()
    {
        $match = new MatchDTO(1, 2, 1, true, 3, 1);

        $score = $match->getOpponentScore(2);

        $this->assertEquals(3, $score);
    }

    public function test_get_opponent_score_team_not_in_match()
    {
        $match = new MatchDTO(1, 2, 1, true, 3, 1);

        $score = $match->getOpponentScore(3);

        $this->assertNull($score);
    }

    public function test_get_opponent_score_unplayed_match()
    {
        $match = new MatchDTO(1, 2, 1, false);

        $score = $match->getOpponentScore(1);

        $this->assertNull($score);
    }

    public function test_to_array_conversion()
    {
        $match = new MatchDTO(1, 2, 3, true, 2, 1);

        $array = $match->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['home_team_id']);
        $this->assertEquals(2, $array['away_team_id']);
        $this->assertEquals(3, $array['week']);
        $this->assertTrue($array['is_played']);
        $this->assertEquals(2, $array['home_score']);
        $this->assertEquals(1, $array['away_score']);
    }

    public function test_to_array_conversion_unplayed_match()
    {
        $match = new MatchDTO(1, 2, 3, false);

        $array = $match->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['home_team_id']);
        $this->assertEquals(2, $array['away_team_id']);
        $this->assertEquals(3, $array['week']);
        $this->assertFalse($array['is_played']);
        $this->assertNull($array['home_score']);
        $this->assertNull($array['away_score']);
    }

    public function test_match_with_draw()
    {
        $match = new MatchDTO(1, 2, 1, true, 2, 2);

        $this->assertEquals(2, $match->getTeamScore(1));
        $this->assertEquals(2, $match->getTeamScore(2));
        $this->assertEquals(2, $match->getOpponentScore(1));
        $this->assertEquals(2, $match->getOpponentScore(2));
    }

    public function test_match_with_zero_scores()
    {
        $match = new MatchDTO(1, 2, 1, true, 0, 0);

        $this->assertEquals(0, $match->getTeamScore(1));
        $this->assertEquals(0, $match->getTeamScore(2));
        $this->assertEquals(0, $match->getOpponentScore(1));
        $this->assertEquals(0, $match->getOpponentScore(2));
    }
}
