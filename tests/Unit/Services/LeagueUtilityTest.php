<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LeagueUtility;
use App\Enums\LeagueStatus;

class LeagueUtilityTest extends TestCase
{
    private LeagueUtility $leagueUtility;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leagueUtility = new LeagueUtility();
    }

    public function test_get_league_status_not_started()
    {
        $status = $this->leagueUtility->getLeagueStatus(0);

        $this->assertEquals(LeagueStatus::NOT_STARTED, $status);
    }

    public function test_get_league_status_in_progress()
    {
        $status = $this->leagueUtility->getLeagueStatus(3);

        $this->assertEquals(LeagueStatus::IN_PROGRESS, $status);
    }

    public function test_get_league_status_completed()
    {
        $status = $this->leagueUtility->getLeagueStatus(config('league.total_weeks'));

        $this->assertEquals(LeagueStatus::COMPLETED, $status);
    }

    public function test_get_league_status_completed_after_total_weeks()
    {
        $status = $this->leagueUtility->getLeagueStatus(config('league.total_weeks') + 1);

        $this->assertEquals(LeagueStatus::COMPLETED, $status);
    }

    public function test_get_progress_percentage_not_started()
    {
        $percentage = $this->leagueUtility->getProgressPercentage(0);

        $this->assertEquals(0, $percentage);
    }

    public function test_get_progress_percentage_halfway()
    {
        $totalWeeks = config('league.total_weeks');
        $halfway = (int) ($totalWeeks / 2);

        $percentage = $this->leagueUtility->getProgressPercentage($halfway);

        $this->assertEquals(50, $percentage);
    }

    public function test_get_progress_percentage_completed()
    {
        $percentage = $this->leagueUtility->getProgressPercentage(config('league.total_weeks'));

        $this->assertEquals(100, $percentage);
    }

    public function test_get_status_label_not_started()
    {
        $label = $this->leagueUtility->getStatusLabel(0);

        $this->assertEquals('Not Started', $label);
    }

    public function test_get_status_label_in_progress()
    {
        $label = $this->leagueUtility->getStatusLabel(3);

        $this->assertEquals('In Progress', $label);
    }

    public function test_get_status_label_completed()
    {
        $label = $this->leagueUtility->getStatusLabel(config('league.total_weeks'));

        $this->assertEquals('Completed', $label);
    }

    public function test_can_simulate_not_started()
    {
        $canSimulate = $this->leagueUtility->canSimulate(0);

        $this->assertTrue($canSimulate);
    }

    public function test_can_simulate_in_progress()
    {
        $canSimulate = $this->leagueUtility->canSimulate(3);

        $this->assertTrue($canSimulate);
    }

    public function test_can_simulate_completed()
    {
        $canSimulate = $this->leagueUtility->canSimulate(config('league.total_weeks'));

        $this->assertFalse($canSimulate);
    }

    public function test_can_simulate_after_completion()
    {
        $canSimulate = $this->leagueUtility->canSimulate(config('league.total_weeks') + 1);

        $this->assertFalse($canSimulate);
    }

    public function test_get_remaining_weeks_not_started()
    {
        $remainingWeeks = $this->leagueUtility->getRemainingWeeks(0);

        $this->assertEquals(config('league.total_weeks'), $remainingWeeks);
    }

    public function test_get_remaining_weeks_halfway()
    {
        $totalWeeks = config('league.total_weeks');
        $halfway = (int) ($totalWeeks / 2);

        $remainingWeeks = $this->leagueUtility->getRemainingWeeks($halfway);

        $this->assertEquals($totalWeeks - $halfway, $remainingWeeks);
    }

    public function test_get_remaining_weeks_completed()
    {
        $remainingWeeks = $this->leagueUtility->getRemainingWeeks(config('league.total_weeks'));

        $this->assertEquals(0, $remainingWeeks);
    }

    public function test_get_remaining_weeks_after_completion()
    {
        $remainingWeeks = $this->leagueUtility->getRemainingWeeks(config('league.total_weeks') + 1);

        $this->assertEquals(0, $remainingWeeks);
    }

    public function test_get_total_weeks()
    {
        $totalWeeks = $this->leagueUtility->getTotalWeeks();

        $this->assertEquals(config('league.total_weeks'), $totalWeeks);
    }
}
