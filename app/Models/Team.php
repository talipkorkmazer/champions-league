<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Team model representing a football team
 *
 * @property int $id
 * @property string $name
 * @property int $strength
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'strength'];

    /**
     * Get the league team relationships for this team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leagueTeams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class);
    }

    /**
     * Get the matches where this team is the home team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matchesAsHome(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'home_team_id');
    }

    /**
     * Get the matches where this team is the away team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matchesAsAway(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'away_team_id');
    }

    /**
     * Get the predictions for this team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}

