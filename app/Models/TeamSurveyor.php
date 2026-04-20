<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSurveyor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'team_surveyors';

    protected $fillable = [
        'team_id',
        'surveyor_id'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }
}