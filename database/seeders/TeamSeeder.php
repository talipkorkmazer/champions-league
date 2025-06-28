<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    public function run()
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

        $count = count($teams); // 20 takım
        foreach ($teams as $index => $name) {
            // 1. takım = 100, sonuncu = 0, aralar eşit
            $strength = round(100 - ($index * (100 / ($count - 1))));

            Team::create([
                'name'     => $name,
                'strength' => $strength,
            ]);
        }
    }
}
