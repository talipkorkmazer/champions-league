<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LeagueMatch model representing a match in a league
 *
 * @property int $id
 * @property int $league_id
 * @property int $week
 * @property int $home_team_id
 * @property int $away_team_id
 * @property int|null $home_score
 * @property int|null $away_score
 * @property bool $is_played
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class LeagueMatch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'league_id',
        'week',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'is_played',
    ];

    /**
     * Get the league that this match belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the home team for this match
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * Get the away team for this match
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
