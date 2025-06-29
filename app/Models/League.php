<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

/**
 * League model representing a football league
 *
 * @property int $id
 * @property string $name
 * @property int $current_week
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class League extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['name', 'current_week'];

    /**
     * Get the teams that belong to this league
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'league_team');
    }

    /**
     * Get the league team relationships for this league
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leagueTeams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class);
    }

    /**
     * Get the matches that belong to this league
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class);
    }

    /**
     * Get the predictions for this league
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
