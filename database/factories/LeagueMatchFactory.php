<?php

namespace Database\Factories;

use App\Models\LeagueMatch;
use App\Models\League;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeagueMatch>
 */
class LeagueMatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeagueMatch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isPlayed = $this->faker->boolean();
        
        return [
            'league_id' => League::factory(),
            'week' => $this->faker->numberBetween(1, 6),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'home_score' => $isPlayed ? $this->faker->numberBetween(0, 5) : null,
            'away_score' => $isPlayed ? $this->faker->numberBetween(0, 5) : null,
            'is_played' => $isPlayed,
        ];
    }

    /**
     * Indicate that the match is unplayed.
     *
     * @return static
     */
    public function unplayed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_played' => false,
            'home_score' => null,
            'away_score' => null,
        ]);
    }

    /**
     * Indicate that the match is played.
     *
     * @return static
     */
    public function played(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_played' => true,
            'home_score' => $this->faker->numberBetween(0, 5),
            'away_score' => $this->faker->numberBetween(0, 5),
        ]);
    }
} 