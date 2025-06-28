<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['name', 'strength'];

    public function leagueTeams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class);
    }

    public function matchesAsHome(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'home_team_id');
    }

    public function matchesAsAway(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'away_team_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}

