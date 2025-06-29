<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\LeagueUtilityDTO;

class LeagueUtilityDTOTest extends TestCase
{
    public function test_league_utility_dto_creation()
    {
        $status = 'in_progress';
        $progressPercentage = 50;
        $statusLabel = 'In Progress';
        $canSimulate = true;
        $remainingWeeks = 3;
        $totalWeeks = 6;

        $dto = new LeagueUtilityDTO(
            $status,
            $progressPercentage,
            $statusLabel,
            $canSimulate,
            $remainingWeeks,
            $totalWeeks
        );

        $this->assertInstanceOf(LeagueUtilityDTO::class, $dto);
        $this->assertEquals($status, $dto->status);
        $this->assertEquals($progressPercentage, $dto->progressPercentage);
        $this->assertEquals($statusLabel, $dto->statusLabel);
        $this->assertEquals($canSimulate, $dto->canSimulate);
        $this->assertEquals($remainingWeeks, $dto->remainingWeeks);
        $this->assertEquals($totalWeeks, $dto->totalWeeks);
    }

    public function test_league_utility_dto_creation_completed_league()
    {
        $status = 'completed';
        $progressPercentage = 100;
        $statusLabel = 'Completed';
        $canSimulate = false;
        $remainingWeeks = 0;
        $totalWeeks = 6;

        $dto = new LeagueUtilityDTO(
            $status,
            $progressPercentage,
            $statusLabel,
            $canSimulate,
            $remainingWeeks,
            $totalWeeks
        );

        $this->assertEquals($status, $dto->status);
        $this->assertEquals($progressPercentage, $dto->progressPercentage);
        $this->assertEquals($statusLabel, $dto->statusLabel);
        $this->assertEquals($canSimulate, $dto->canSimulate);
        $this->assertEquals($remainingWeeks, $dto->remainingWeeks);
        $this->assertEquals($totalWeeks, $dto->totalWeeks);
    }

    public function test_league_utility_dto_creation_not_started()
    {
        $status = 'not_started';
        $progressPercentage = 0;
        $statusLabel = 'Not Started';
        $canSimulate = true;
        $remainingWeeks = 6;
        $totalWeeks = 6;

        $dto = new LeagueUtilityDTO(
            $status,
            $progressPercentage,
            $statusLabel,
            $canSimulate,
            $remainingWeeks,
            $totalWeeks
        );

        $this->assertEquals($status, $dto->status);
        $this->assertEquals($progressPercentage, $dto->progressPercentage);
        $this->assertEquals($statusLabel, $dto->statusLabel);
        $this->assertEquals($canSimulate, $dto->canSimulate);
        $this->assertEquals($remainingWeeks, $dto->remainingWeeks);
        $this->assertEquals($totalWeeks, $dto->totalWeeks);
    }

    public function test_to_array_conversion()
    {
        $dto = new LeagueUtilityDTO(
            'in_progress',
            50,
            'In Progress',
            true,
            3,
            6
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('in_progress', $array['status']);
        $this->assertEquals(50, $array['progressPercentage']);
        $this->assertEquals('In Progress', $array['statusLabel']);
        $this->assertTrue($array['canSimulate']);
        $this->assertEquals(3, $array['remainingWeeks']);
        $this->assertEquals(6, $array['totalWeeks']);
    }

    public function test_to_array_conversion_completed_league()
    {
        $dto = new LeagueUtilityDTO(
            'completed',
            100,
            'Completed',
            false,
            0,
            6
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('completed', $array['status']);
        $this->assertEquals(100, $array['progressPercentage']);
        $this->assertEquals('Completed', $array['statusLabel']);
        $this->assertFalse($array['canSimulate']);
        $this->assertEquals(0, $array['remainingWeeks']);
        $this->assertEquals(6, $array['totalWeeks']);
    }

    public function test_dto_properties_are_readonly()
    {
        $dto = new LeagueUtilityDTO(
            'in_progress',
            50,
            'In Progress',
            true,
            3,
            6
        );

        $this->assertEquals('in_progress', $dto->status);
        $this->assertEquals(50, $dto->progressPercentage);
        $this->assertEquals('In Progress', $dto->statusLabel);
        $this->assertTrue($dto->canSimulate);
        $this->assertEquals(3, $dto->remainingWeeks);
        $this->assertEquals(6, $dto->totalWeeks);
    }
}
