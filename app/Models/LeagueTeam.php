<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueTeam extends Model
{
    protected $fillable = ['league_id', 'team_id'];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
