<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'status',
    ];

    // A team member belongs to a team
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // A team member refers to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
