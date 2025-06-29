<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\LeagueStatus;

class LeagueStatusTest extends TestCase
{
    public function test_not_started_case()
    {
        $status = LeagueStatus::NOT_STARTED;

        $this->assertEquals('not_started', $status->value);
        $this->assertEquals('Not Started', $status->label());
    }

    public function test_in_progress_case()
    {
        $status = LeagueStatus::IN_PROGRESS;

        $this->assertEquals('in_progress', $status->value);
        $this->assertEquals('In Progress', $status->label());
    }

    public function test_completed_case()
    {
        $status = LeagueStatus::COMPLETED;

        $this->assertEquals('completed', $status->value);
        $this->assertEquals('Completed', $status->label());
    }

    public function test_all_cases_exist()
    {
        $cases = LeagueStatus::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(LeagueStatus::NOT_STARTED, $cases);
        $this->assertContains(LeagueStatus::IN_PROGRESS, $cases);
        $this->assertContains(LeagueStatus::COMPLETED, $cases);
    }

    public function test_enum_values_are_unique()
    {
        $cases = LeagueStatus::cases();
        $values = array_map(fn($case) => $case->value, $cases);

        $this->assertCount(count($values), array_unique($values));
    }

    public function test_enum_labels_are_unique()
    {
        $cases = LeagueStatus::cases();
        $labels = array_map(fn($case) => $case->label(), $cases);

        $this->assertCount(count($labels), array_unique($labels));
    }

    public function test_enum_can_be_used_in_comparisons()
    {
        $status1 = LeagueStatus::NOT_STARTED;
        $status2 = LeagueStatus::NOT_STARTED;
        $status3 = LeagueStatus::IN_PROGRESS;

        $this->assertTrue($status1 === $status2);
        $this->assertFalse($status1 === $status3);
        $this->assertTrue($status1 !== $status3);
    }

    public function test_enum_can_be_used_in_switch_statements()
    {
        $status = LeagueStatus::COMPLETED;

        $result = match ($status) {
            LeagueStatus::NOT_STARTED => 'not_started',
            LeagueStatus::IN_PROGRESS => 'in_progress',
            LeagueStatus::COMPLETED => 'completed',
        };

        $this->assertEquals('completed', $result);
    }

    public function test_enum_can_be_serialized()
    {
        $status = LeagueStatus::IN_PROGRESS;

        $serialized = serialize($status);
        $unserialized = unserialize($serialized);

        $this->assertEquals($status, $unserialized);
        $this->assertEquals('in_progress', $unserialized->value);
        $this->assertEquals('In Progress', $unserialized->label());
    }

    public function test_enum_can_be_used_as_array_key()
    {
        $statusMap = [
            LeagueStatus::NOT_STARTED->value => 'League has not started',
            LeagueStatus::IN_PROGRESS->value => 'League is in progress',
            LeagueStatus::COMPLETED->value => 'League is completed'
        ];

        $this->assertEquals('League has not started', $statusMap[LeagueStatus::NOT_STARTED->value]);
        $this->assertEquals('League is in progress', $statusMap[LeagueStatus::IN_PROGRESS->value]);
        $this->assertEquals('League is completed', $statusMap[LeagueStatus::COMPLETED->value]);
    }

    public function test_enum_can_be_used_in_array_functions()
    {
        $statuses = [LeagueStatus::NOT_STARTED, LeagueStatus::IN_PROGRESS, LeagueStatus::COMPLETED];

        $values = array_map(fn($status) => $status->value, $statuses);
        $labels = array_map(fn($status) => $status->label(), $statuses);

        $this->assertEquals(['not_started', 'in_progress', 'completed'], $values);
        $this->assertEquals(['Not Started', 'In Progress', 'Completed'], $labels);
    }
}
