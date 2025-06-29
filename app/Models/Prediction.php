<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Prediction model representing championship predictions for teams
 *
 * @property int $id
 * @property int $league_id
 * @property int $week
 * @property int $team_id
 * @property float $percentage
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Prediction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['league_id', 'week', 'team_id', 'percentage'];

    /**
     * Get the league that this prediction belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the team that this prediction is for
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
