<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

/**
 * Seeder for populating the teams table with Premier League teams
 */
class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 20 Premier League teams with decreasing strength values
     * from 100 (strongest) to 0 (weakest)
     *
     * @return void
     */
    public function run(): void
    {
        $teams = [
            'Manchester City',
            'Arsenal',
            'Manchester United',
            'Newcastle United',
            'Liverpool',
            'Brighton And Hove Albion',
            'Aston Villa',
            'Tottenham Hotspur',
            'Brentford',
            'Fulham',
            'Crystal Palace',
            'Chelsea',
            'Wolverhampton Wanderers',
            'West Ham United',
            'Bournemouth',
            'Nottingham Forest',
            'Everton',
            'Leicester City',
            'Leeds United',
            'Southampton',
        ];

        $count = count($teams);
        foreach ($teams as $index => $name) {
            $strength = round(100 - ($index * (100 / ($count - 1))));

            Team::create([
                'name'     => $name,
                'strength' => $strength,
            ]);
        }
    }
}
