<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = ['name', 'current_week'];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'league_team');
    }

    public function leagueTeams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
